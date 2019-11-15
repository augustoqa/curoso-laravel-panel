<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use SoftDeletes;

    //protected $table = 'users';

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        //
    ];

    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return;
        }

        $query->where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('team', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
        });
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
