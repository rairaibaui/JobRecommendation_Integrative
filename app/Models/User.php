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
        if (!empty($this->attributes['date_of_birth'])) {
            try {
                return Carbon::parse($this->attributes['date_of_birth']);
            } catch (\Throwable $e) {
                return null;
            }
        }

        // If there is an explicitly stored birthday attribute, return it (covers some older codepaths)
        if (!empty($this->attributes['birthday'])) {
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
        if (empty($value)) {
            $this->attributes['date_of_birth'] = null;
            $this->attributes['birthday'] = null;
            return;
        }

        try {
            $d = Carbon::parse($value);
            $this->attributes['date_of_birth'] = $d->format('Y-m-d');
            // Also keep birthday attribute synchronized for consumers that expect it
            $this->attributes['birthday'] = $d->format('Y-m-d');
        } catch (\Throwable $e) {
            // If parsing fails, store raw value into birthday and leave date_of_birth null
            $this->attributes['birthday'] = $value;
        }
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

    /**
     * Override the default email verification notification to use our
     * custom notification which respects the configured expiry.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }
}
