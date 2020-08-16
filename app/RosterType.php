<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class RosterType extends Model
{
    public static function sync()
    {
        $rosterTypes = static::all();
        foreach (API::get()->rosterTypes() as $incoming) {
            if (!$rosterTypes->contains('mlb_id', '=', $incoming['parameter'])) {
                $new = new static();
                $new->mlb_id = $incoming['parameter'];
                $new->description = $incoming['description'];
                $new->save();
            }
        }
    }
}
