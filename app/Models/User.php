<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'birthday' => 'date',
        'education' => 'array',
        'experiences' => 'array',
        'years_of_experience' => 'integer',
    ];

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
}
