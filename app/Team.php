<?php

namespace App;

use App\MLB\API;
use App\Traits\CachesApiData;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use CachesApiData;

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

    public static function sync($id = null)
    {
        static::unguard();

        $incoming = API::get()->team($id);

        $venue = Venue::getOrLoad($incoming['venue']['id']);
        $league = League::getOrLoad($incoming['league']['id']);
        $division = Division::getOrLoad($incoming['division']['id']);
        $sport = Sport::getOrLoad($incoming['sport']['id']);
        if ($parentMlbId = $incoming['parentOrgId'] ?? null) {
            $parent = Team::getOrLoad($parentMlbId);
        }

        return static::updateOrCreate(['mlb_id' => $id], [
            'mlb_file_code' => $incoming['fileCode'],
            'name' => $incoming['name'],
            'code' => $incoming['teamCode'],
            'abbrev' => $incoming['abbreviation'],
            'location' => $incoming['locationName'],
            'first_played' => $incoming['firstYearOfPlay'],
            'venue_id' => $venue->id ?? null,
            'league_id' => $league->id ?? null,
            'division_id' => $division->id ?? null,
            'sport_id' => $sport->id ?? null,
            'parent_team_id' => $parent->id ?? null,
        ]);
    }
}
