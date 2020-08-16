<?php

namespace App;

use App\MLB\API;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $dates = ['datetime', 'rescheduled_from'];

    protected $casts = [
        'double_header' => 'boolean',
        'tiebreaker' => 'boolean',
    ];

    public static function sync()
    {
        static::unguard();

        $venues = Venue::all();

        foreach (API::get()->schedule()['dates'] as $date) {
            foreach ($date['games'] as $incoming) {

                $venue = $venues->firstWhere('mlb_id', '=', $incoming['venue']['id']);
                if (!$venue) {
                    $venue = Venue::sync($incoming['venue']['id']);
                    $venues->push($venue);
                }

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
                    'inning_break_length' => $incoming['inningBreakLength'],
                    'games_in_series' => $incoming['gamesInSeries'],
                    'series_game_number' => $incoming['seriesGameNumber'],
                    'series_description' => $incoming['seriesDescription'] ?? null,
                    'mlb_record_source' => $incoming['recordSource'],

                    'type_id' => null,
                    'status_id' => null,
                    'home_team_id' => null,
                    'away_team_id' => null,
                    'venue_id' => $venue->id,
                ]);
            }
        }
    }
}
