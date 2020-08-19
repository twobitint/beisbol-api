<?php

namespace App;

use App\MLB\API;
use App\Traits\CachesApiData;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use CachesApiData;

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public static function sync()
    {
        $divisions = static::all();
        $leagues = League::all();
        $sports = Sport::all();
        foreach (API::get()->divisions() as $incoming) {
            if (!$divisions->contains('mlb_id', '=', $incoming['id'])) {
                $new = new static();
                $new->mlb_id = $incoming['id'];
                $new->name = $incoming['name'];
                $new->abbrev = $incoming['abbreviation'];

                if ($sportId = $incoming['sport']['id'] ?? false) {
                    if ($sport = $sports->firstWhere('mlb_id', '=', $sportId)) {
                        $new->sport_id = $sport->id;
                    }
                }

                if ($leagueId = $incoming['league']['id'] ?? false) {
                    if ($league = $leagues->firstWhere('mlb_id', '=', $leagueId)) {
                        $new->league_id = $league->id;
                    }
                }

                $new->save();
            }
        }
    }
}
