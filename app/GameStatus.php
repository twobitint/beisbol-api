<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class GameStatus extends Model
{
    public static function sync()
    {
        $statuses = static::all();
        foreach (API::get()->gameStatuses() as $incoming) {
            if (!$statuses->contains('mlb_id', '=', $incoming['statusCode'])) {
                $new = new static();
                $new->mlb_id = $incoming['statusCode'];
                $new->description = $incoming['detailedState'];
                $new->game_code = $incoming['codedGameState'];
                $new->abstract_description = $incoming['abstractGameState'];
                $new->abstract_code = $incoming['abstractGameCode'];
                $new->reason = $incoming['reason'] ?? null;
                $new->save();
            }
        }
    }
}
