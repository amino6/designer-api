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
        'available_to_hire',
        'contact_email',
        'website_url',
        'job_title'
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

    protected $appends = [
        'photo_url'
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

    public function getPhotoUrlAttribute()
    {
        $headers = get_headers('https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?d=404');
        $response_code = substr($headers[0], 9, 3);
        if($response_code == '200') {
            return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email));
        }else {
            return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . 'jpg?s=200&d=mm';
        }
    }

    protected function scopeSearch($query, Request $request)
    {
        if ($request->has_designs) {
            $query->whereHas('designs', function ($query) {
                $query->where('is_live', true);
            });
        }

        if ($request->q) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance ?? 0;
        $unit = $request->unit;

        if ($lat && $lng) {
            $point = new Point($lat, $lng);
            // convert distance to meters
            if (strtolower($unit) === 'km') {
                $dist *= 1000;
            } else {
                $dist *= 1609.34;
            }

            if ($dist > 0) {
                $query->whereDistanceSphere('location', $point,  '<', $dist);
            }else {
                $query->whereDistanceSphere('location', $point,  '<', 10000);
            }
        }

        if ($request->orderBy == 'closest' && isset($point)) {
            $query->orderByDistanceSphere('location', $point, 'asc');
        } else {
            $query->withCount('designs')->orderBy('designs_count', 'desc');
        }

        return $query;
    }
}
