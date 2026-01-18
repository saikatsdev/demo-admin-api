<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Contracts\LaratrustUser;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\HasRolesAndPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements LaratrustUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasRolesAndPermissions;

    public $uploadPath = 'uploads/users';

    protected $fillable = [
        'username',
        'phone_number',
        'email',
        'verification_otp',
        'password',
        'status',
        'is_verified',
        'img_path',
        'bonus_points',
        'user_category_id',
        'manager_id',
        'home_address',
        'office_address',
        'dob',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
    ];

    public function userCategory(): BelongsTo
    {
        return $this->belongsTo(UserCategory::class, "user_category_id", "id");
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, "manager_id", "id");
    }
}
