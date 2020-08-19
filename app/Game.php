<?php

namespace App;

use App\MLB\API;
use App\Traits\CachesApiData;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use CachesApiData;

    protected $dates = ['datetime', 'rescheduled_from'];

    protected $casts = [
        'double_header' => 'boolean',
        'tiebreaker' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(GameType::class);
    }

    public function status()
    {
        return $this->belongsTo(GameStatus::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function home()
    {
        return $this->belongsTo(Team::class);
    }

    public function away()
    {
        return $this->belongsTo(Team::class);
    }

    public static function sync()
    {
        static::unguard();

        foreach (API::get()->schedule()['dates'] as $date) {
            foreach ($date['games'] as $incoming) {

                $type = GameType::getOrLoad($incoming['gameType']);
                $status = GameStatus::where('mlb_id', '=', $incoming['status']['statusCode'])->first();
                $home = Team::getOrLoad($incoming['teams']['home']['team']['id']);
                $away = Team::getOrLoad($incoming['teams']['away']['team']['id']);
                $venue = Venue::getOrLoad($incoming['venue']['id']);

                static::UpdateOrCreate(['mlb_id' => $incoming['gamePk']], [
                    'season' => $incoming['season'],
                    'datetime' => $incoming['gameDate'],
                    'rescheduled_from' => $incoming['rescheduledFrom'] ?? null,
                    'tie' => $incoming['isTie'] ?? null,
                    'number' => $incoming['gameNumber'],
                    'double_header' => $incoming['doubleHeader'] == 'Y',
                    'mlb_gameday_id' => $incoming['gamedayType'],
                    'tiebreaker' => $incoming['tiebreaker'] == 'Y',
                    'daynight' => $incoming['dayNight'],
                    'description' => $incoming['description'] ?? null,
                    'scheduled_innings' => $incoming['scheduledInnings'],
                    'inning_break_length' => $incoming['inningBreakLength'] ?? null,
                    'games_in_series' => $incoming['gamesInSeries'],
                    'series_game_number' => $incoming['seriesGameNumber'],
                    'series_description' => $incoming['seriesDescription'] ?? null,
                    'mlb_record_source' => $incoming['recordSource'],

                    'type_id' => $type ? $type->id : null,
                    'status_id' => $status ? $status->id : null,
                    'home_team_id' => $home->id,
                    'away_team_id' => $away->id,
                    'venue_id' => $venue ? $venue->id : null,
                ]);
            }
        }
    }
}
