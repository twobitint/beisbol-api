<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public static function sync()
    {
        foreach (API::get()->schedule()['dates'] as $date) {
            foreach ($date['games'] as $incoming) {
                static::UpdateOrCreate(['mlb_id' => $incoming['gamePk']], [

                ]);
            }
        }
    }
}
