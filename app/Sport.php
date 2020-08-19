<?php

namespace App;

use App\MLB\API;
use App\Traits\CachesApiData;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use CachesApiData;

    public function leagues()
    {
        return $this->hasMany(League::class);
    }

    public static function sync()
    {
        $sports = static::all();
        foreach (API::get()->sports() as $incoming) {
            if (!$sports->contains('mlb_id', '=', $incoming['id'])) {
                $new = new static();
                $new->mlb_id = $incoming['id'];
                $new->code = $incoming['code'];
                $new->name = $incoming['name'];
                $new->abbrev = $incoming['abbreviation'];
                $new->save();
            }
        }
    }
}
