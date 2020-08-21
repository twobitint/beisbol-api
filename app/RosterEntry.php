<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RosterEntry extends Model
{
    protected $dates = ['start', 'end'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function rosterType()
    {
        return $this->belongsTo(RosterType::class);
    }

    public function rosterStatus()
    {
        return $this->belongsTo(RosterStatus::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeCurrent(Builder $query)
    {
        $current = DB::table('roster_entries')
            ->select(DB::raw('player_id, max(start) as latest'))
            ->groupBy('player_id');
        $query->joinSub($current, 'current', function ($join) {
            $join->on('roster_entries.player_id', '=', 'current.player_id')
                ->on('roster_entries.start', '=', 'current.latest');
        });
    }

    public static function sync()
    {
        // $rosterTypes = static::where('')
        // foreach (API::get()->roster() as $incoming) {
        //     if (!$rosterTypes->contains('mlb_id', '=', $incoming['parameter'])) {
        //         $new = new static();
        //         $new->mlb_id = $incoming['parameter'];
        //         $new->description = $incoming['description'];
        //         $new->save();
        //     }
        // }
    }
}
