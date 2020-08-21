<?php

namespace App;

use App\MLB\API;
use App\Traits\CachesApiData;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use CachesApiData;

    public static function sync($ids)
    {
        $multiple = is_array($ids);

        static::unguard();

        $data = API::get()->players($multiple ? $ids : [$ids]);

        $return = [];
        foreach ($data as $incoming) {
            $new = static::updateOrCreate(['mlb_id' => $incoming['id']], [
                'full_name' => $incoming['fullName'],
            ]);
            $return[] = $new;
        }

        return $multiple ? $return : $return[0];
    }
}
