<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Controllers\Notifications\CustomResetPasswordNotification;
use App\Models\Admin\MediaLibraries;
use App\Models\Admin\Role;
use App\Models\Admin\ServiceProvider;
use App\Models\UserMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'zoho_contact_id',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'password',
        'role_id',
        'org_id',
        'sub_org_id',
        'profile_img_media_id',
        'service_provider_id',
        'active',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'cardholder_name',
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

    /* Relationships */
    public function role()
    {
        return $this->belongsTo(Role::class)->where('service_provider_id', $this->serviceProvider->id);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class)->where('active',1);
    }
    /* Other relationships */
    public function hasServiceProviderAccess()
    {
        return $this->serviceProvider !== null && $this->service_provider_id == $this->serviceProvider->id;
    }

    /* service provider roles access */
    public function hasRole($role)
    {
        return $this->role && $this->role->role_type == $role;
    }

    /* User Profile Image */
    public function profileImage()
    {
        if (!$this->profile_img_media_id) {
            return null;
        }

        $media = MediaLibraries::select('file_name', 'file_path')
            ->where('id', $this->profile_img_media_id)
            ->first();

        return $media ? $media->file_path . $media->file_name : null;
    }

     /* User Role Details */
     public function roleFor()
     {
         if (!$this->role_id) {
             return null;
         }

         $role = Role::select('role_for')
             ->where('id', $this->role_id)
             ->first();

         return $role ? $role->role_for : null;
     }

    /** User Meta */
    public function userMetaKey() {
        $userMeta = UserMeta::select('meta_key','meta_value')->where('user_id', $this->id)->where('active', 1)->where('service_provider_id', $this->service_provider_id)->first();
        return $userMeta ? $userMeta : null;
    }



    // protected $appends = ['user_meta'];

    // public function getUserMetaAttribute() {
    //     return $this->userMetaKey();
    // }





}
