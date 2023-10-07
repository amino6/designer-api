<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasSpatial;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'about',
        'location',
        'formatted_address',
        'available_to_hire'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'location' => Point::class,
    ];

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderBy('created_at', 'asc');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, "user_team");
    }

    public function ownedTeams()
    {
        return $this->teams()->where('owner_id', $this->id);
    }

    public function isOwnerOfTeam($team_id)
    {
        return (bool) $this->teams()->where('team_id', $team_id)->where('owner_id', $this->id)->count();
    }

    public function invitaions()
    {
        return $this->hasMany(Invitation::class, 'recipient_email', 'email');
    }

    protected function scopeSearch($query, Request $request)
    {
        if ($request->has_design) {
            $query->has('designs');
        }

        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if ($lat && $lng) {
            $point = new Point($lat, $lng);
            // convert distance to meters
            $unit == 'km' ? $dist *= 1000 : $dist *= 1609.34;
            $query->whereDistanceSphere('location', $point,  '<', $dist);
        }

        if ($request->orderBy == 'closest' && isset($point)) {
            $query->orderByDistanceSphere('location', $point, 'asc');
        } else if ($request->orderBy == 'latest') {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query;
    }
}
