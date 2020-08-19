<?php

namespace App;

use App\MLB\API;
use App\Traits\CachesApiData;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use CachesApiData;

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public static function sync()
    {
        $leagues = static::all();
        $sports = Sport::all();
        foreach (API::get()->leagues() as $incoming) {
            if (!$leagues->contains('mlb_id', '=', $incoming['id'])) {
                $new = new static();
                $new->mlb_id = $incoming['id'];
                $new->name = $incoming['name'];
                $new->abbrev = $incoming['abbreviation'];

                if ($sportId = $incoming['sport']['id'] ?? false) {
                    if ($sport = $sports->firstWhere('mlb_id', '=', $sportId)) {
                        $new->sport_id = $sport->id;
                    }
                }

                $new->save();
            }
        }
    }
}
