<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_title',
        'company_name',
        'business_type',
        'industry',
        'years_in_operation',
        'website',
        'sourcing_categories',
        'import_volume',
        'hear_about',
        'business_registration_path',
        'import_license_path',
        'tax_id',
        'country',
        'city',
        'password',
        'status',
        'avatar_path',
        'first_login_at',
        'approval_message',
        'admin_requested_info',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'first_login_at' => 'datetime',
            'sourcing_categories' => 'array',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function factory(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Factory::class);
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }
        $presets = config('avatars.presets', []);
        if (isset($presets[$this->avatar_path])) {
            $base = rtrim(config('avatars.base_url', 'https://api.dicebear.com/7.x/avataaars/svg'), '/');
            return $base . '?seed=' . urlencode($presets[$this->avatar_path]);
        }
        // File path (from admin user create/edit upload)
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar_path);
    }
}
