<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ApiConfig;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'numero',
        'is_provider',
        'password',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the API configurations for the user.
     */
    public function apiConfigs()
    {
        return $this->hasMany(ApiConfig::class);
    }

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission($permission)
    {
        // If user is admin (has 'admin' role), they have all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        // Check if any of the user's roles have this permission
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user can view all data or only their own.
     */
    public function canViewAllData()
    {
        // Check if user has the 'view_all_data' permission
        return $this->hasPermission('view_all_data');
    }

    /**
     * Get the provider queue entry for the user.
     */
    public function providerQueueEntry()
    {
        return $this->hasOne(FilaPrestador::class, 'user_id');
    }

    /**
     * Check if the user is a service provider.
     */
    public function isProvider()
    {
        return $this->is_provider;
    }
}