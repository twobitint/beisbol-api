<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    // public function teams()
    // {
    //     return $this->hasMany(Team::class);
    // }

    public static function sync($id = null)
    {
        static::unguard();

        if ($id) {
            $incoming = API::get()->venue($id);
            return static::updateOrCreate(['mlb_id' => $id], [
                'name' => $incoming['name'],
            ]);
        }

        $venues = static::all();
        foreach (API::get()->venues() as $incoming) {
            if (!$venues->contains('mlb_id', '=', $incoming['id'])) {
                $new = new static();
                $new->mlb_id = $incoming['id'];
                $new->name = $incoming['name'] ?? 'Unknown';
                $new->save();
            }
        }
    }
}
