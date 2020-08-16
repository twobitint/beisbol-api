<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function parentTeam()
    {
        return $this->belongsTo(Team::class);
    }
}
