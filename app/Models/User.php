<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->belongsToMany(Idea::class, 'votes');
    }

    public function getAvatar()
    {
        $firstCharacter = $this->email[0];

        $integerToUse = is_numeric($firstCharacter)
            ? ord(strtolower($firstCharacter)) - 21
            : ord(strtolower($firstCharacter)) - 96;

        return 'https://www.gravatar.com/avatar/'
            . md5($this->email)
            . '?s=200'
            . '&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-'
            . $integerToUse
            . '.png';
    }

    public function isAdmin()
    {
        return in_array($this->email, [
            'phuclh93@gmail.com'
        ]);
    }

    /**
     * Create user from user's data of DeKnot.
     *
     * @param SocialiteUser $user
     * @return mixed
     */
    public static function createUserFromDeKnot(SocialiteUser $user)
    {
        return User::firstOrCreate([
            'email' => $user->getEmail()
        ], [
            'name'              => $user->getName(),
            'email_verified_at' => $user->user['is_verified'] ? now() : null
        ]);
    }
}
