<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public static function sync()
    {
        $incoming = API::get()->games();

        // foreach (API::get()->sports() as $incoming) {
        //     if (!$sports->contains('mlb_id', '=', $incoming['id'])) {
        //         $new = new static();
        //         $new->mlb_id = $incoming['id'];
        //         $new->code = $incoming['code'];
        //         $new->name = $incoming['name'];
        //         $new->abbrev = $incoming['abbreviation'];
        //         $new->save();
        //     }
        // }
    }
}
