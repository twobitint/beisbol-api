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

    public function fortyManRoster()
    {
        return $this->roster('40Man');
    }

    public function activeRoster()
    {
        return $this->roster();
    }

    protected function roster($type = 'active')
    {
        return $this->hasMany(RosterEntry::class)
            ->current()
            ->whereHas('rosterType', function ($query) use ($type) {
                $query->where('roster_types.mlb_id', '=', $type);
            })->with('player');
    }

    public function syncFortyManRoster()
    {
        $this->syncRoster('40Man');
    }

    public function syncActiveRoster()
    {
        $this->syncRoster('active');
    }

    public function getApiUrlAttribute()
    {
        return API::url()->team($this->mlb_id);
    }

    protected function syncRoster($typeId = null)
    {
        static::unguard();

        $type = RosterType::where('mlb_id', '=', $typeId)->first();
        $roster = ($typeId == 'active' ? $this->activeRoster : $this->fortyManRoster)
            ->keyBy('player.mlb_id');
        $liveRosterData = API::get()->type($typeId)->roster($this->mlb_id);

        // sync players not on the roster.
        $toSync = [];
        foreach ($liveRosterData as $incoming) {
            if (!$roster->has($incoming['person']['id'])) {
                $toSync[] = $incoming['person']['id'];
            }
        }
        if ($toSync) {
            Player::sync($toSync);
        }

        foreach ($liveRosterData as $incoming) {
            $status = RosterStatus::fromData($incoming['status']);
            $player = Player::getOrLoad($incoming['person']['id']);
            $entry = $roster->firstWhere('player_id', '=', $player->id);

            // deal with a new or modified entry.
            if (!$entry || $entry->roster_status_id != $status->id) {
                RosterEntry::create([
                    'start' => now(),
                    'roster_type_id' => $type->id,
                    'player_id' => $player->id,
                    'team_id' => $this->id,
                    'roster_status_id' => $status->id,
                ]);

                if ($entry) {
                    $entry->update(['end' => now()->subDay()]);
                }
            }
        }

        // remove players who are not in the live data.
        foreach ($roster as $rosterEntry) {
            $found = array_filter($liveRosterData, function ($incoming) use ($rosterEntry) {
                return $incoming['person']['id'] == $rosterEntry->player->mlb_id;
            });
            if (!$found) {
                $rosterEntry->update(['end' => now()->subDay()]);
            }
        }
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
