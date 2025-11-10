<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'job_title',
        'email',
        'phone_number',
        'birthday',
        'education_level',
        'skills',
        'years_of_experience',
        'location',
        'address',
        'summary',
        'education',
        'experiences',
        'languages',
        'portfolio_links',
        'availability',
        'resume_file',
        'business_permit_path',
        'user_type',
        'password',
        'profile_picture',
        'employment_status',
        'hired_by_company',
        'hired_date',
        'is_admin',
        'role',
        'resume_verification_status',
        'verification_flags',
        'verification_score',
        'verified_at',
        'verification_notes',
        // Backwards-compatible DB column (some migrations created `date_of_birth`)
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // Keep birthday as a convenient attribute, and also cast the underlying column
        'birthday' => 'date',
        'date_of_birth' => 'date',
        'education' => 'array',
        'experiences' => 'array',
        'years_of_experience' => 'integer',
        'hired_date' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * Accessor for `birthday` to read from `date_of_birth` column for backwards compatibility.
     */
    public function getBirthdayAttribute()
    {
        \Log::info('getBirthdayAttribute called', [
            'date_of_birth' => $this->attributes['date_of_birth'] ?? 'not set',
            'birthday' => $this->attributes['birthday'] ?? 'not set',
            'date_of_birth_type' => gettype($this->attributes['date_of_birth'] ?? null),
            'birthday_type' => gettype($this->attributes['birthday'] ?? null),
        ]);

        if (!empty($this->attributes['date_of_birth'])) {
            // If it's already a Carbon instance, return it directly
            if ($this->attributes['date_of_birth'] instanceof Carbon) {
                return $this->attributes['date_of_birth'];
            }
            try {
                return Carbon::parse($this->attributes['date_of_birth']);
            } catch (\Throwable $e) {
                return null;
            }
        }

        // If there is an explicitly stored birthday attribute, return it (covers some older codepaths)
        if (!empty($this->attributes['birthday'])) {
            // If it's already a Carbon instance, return it directly
            if ($this->attributes['birthday'] instanceof Carbon) {
                return $this->attributes['birthday'];
            }
            try {
                return Carbon::parse($this->attributes['birthday']);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Mutator for `birthday` to write into `date_of_birth` column for backwards compatibility.
     */
    public function setBirthdayAttribute($value)
    {
        \Log::info('setBirthdayAttribute called', [
            'value' => $value,
            'value_type' => gettype($value),
            'is_carbon' => $value instanceof Carbon,
            'attributes_before' => $this->attributes['birthday'] ?? 'not set',
        ]);

        if (empty($value)) {
            $this->attributes['date_of_birth'] = null;
            $this->attributes['birthday'] = null;
            return;
        }

        // If value is already a Carbon instance, use it directly
        if ($value instanceof Carbon) {
            $d = $value;
        } else {
            try {
                $d = Carbon::parse($value);
            } catch (\Throwable $e) {
                // If parsing fails, store raw value into birthday and leave date_of_birth null
                $this->attributes['birthday'] = $value;
                return;
            }
        }

        $this->attributes['date_of_birth'] = $d->format('Y-m-d');
        // Also keep birthday attribute synchronized for consumers that expect it
        $this->attributes['birthday'] = $d->format('Y-m-d');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('read', false);
    }

    public function documentValidations()
    {
        return $this->hasMany(DocumentValidation::class);
    }

    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class, 'employer_id');
    }

    /**
     * Override the default email verification notification to use our
     * custom notification which respects the configured expiry.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }
}
