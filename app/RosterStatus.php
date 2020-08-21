<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class RosterStatus extends Model
{
    protected static $cache;

    public function rosterEntries()
    {
        return $this->hasMany(RosterEntry::class);
    }

    public static function fromData($incoming)
    {
        if (!static::$cache) {
            static::$cache = static::all();
        }

        $status = static::$cache->firstWhere('code', '=', $incoming['code']);

        if (!$status) {
            $status = new static();
            $status->code = $incoming['code'];
            $status->description = $incoming['description'];
            $status->save();
            static::$cache->push($status);
        }

        return $status;
    }

    public static function sync()
    {
        // $rosterTypes = static::all();
        // foreach (API::get()->rosterTypes() as $incoming) {
        //     if (!$rosterTypes->contains('mlb_id', '=', $incoming['parameter'])) {
        //         $new = new static();
        //         $new->mlb_id = $incoming['parameter'];
        //         $new->description = $incoming['description'];
        //         $new->save();
        //     }
        // }
    }
}
