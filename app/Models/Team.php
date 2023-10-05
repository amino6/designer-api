<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'slug'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, "user_team");
    }

    public function hasUser($user_id)
    {
        return (bool) $this->members->where('id', $user_id)->count();
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function hasPendingInvite($email)
    {
        return (bool) $this->invitations()->where('recipient_email', $email)->count();
    }
}
