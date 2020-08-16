<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    public static function sync()
    {
        $gameTypes = static::all();
        foreach (API::get()->gameTypes() as $incoming) {
            if (!$gameTypes->contains('mlb_id', '=', $incoming['id'])) {
                $new = new static();
                $new->mlb_id = $incoming['id'];
                $new->description = $incoming['description'];
                $new->save();
            }
        }
    }
}
