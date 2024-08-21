<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'occupation',
        'connect'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id', 'id')->orderBy('id', 'desc');
    }

    public function proposals()
    {
        return $this->hasMany(ProjectApplicant::class, 'freelancer_id', 'id')->orderBy('id', 'desc');
    }

    public function hasAppliedToProject($project_id)
    {
        return ProjectApplicant::where('project_id', $project_id)
            ->where('freelancer_id', $this->id)
            ->exists();
    }
}
