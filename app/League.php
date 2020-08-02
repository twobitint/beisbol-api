<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $guarded = [];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function users()
    {
        return $this->hasManyDeep(User::class, [Team::class, 'team_user']);
    }

    public function thread()
    {
        return $this->morphOne(Thread::class, 'threadable');
    }
}
