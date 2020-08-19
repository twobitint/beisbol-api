<?php

namespace App\Traits;

trait CachesApiData
{
    protected static $cache;

    public static function getOrLoad($mlbId)
    {
        if (!static::$cache) {
            static::$cache = static::all();
        }

        $result = static::$cache->firstWhere('mlb_id', '=', $mlbId);
        if (!$result) {
            if ($result = static::sync($mlbId)) {
                static::$cache->push($result);
            }
        }

        return $result;
    }
}
