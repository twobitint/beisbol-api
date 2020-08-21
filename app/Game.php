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

    public function homeTeam()
    {
        return $this->belongsTo(Team::class);
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class);
    }

    public function winningTeam()
    {
        return $this->belongsTo(Team::class);
    }

    public function losingTeam()
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
                $homeTeam = Team::getOrLoad($incoming['teams']['home']['team']['id']);
                $awayTeam = Team::getOrLoad($incoming['teams']['away']['team']['id']);
                $winningTeam = null;
                $losingTeam = null;
                if ($incoming['teams']['home']['isWinner'] ?? false) {
                    $winningTeam = $homeTeam;
                    $losingTeam = $awayTeam;
                } elseif ($incoming['teams']['away']['isWinner'] ?? false) {
                    $winningTeam = $awayTeam;
                    $losingTeam = $homeTeam;
                }
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

                    'away_record_wins' => $incoming['teams']['away']['leagueRecord']['wins'],
                    'away_record_losses' => $incoming['teams']['away']['leagueRecord']['losses'],
                    'away_score' => $incoming['teams']['away']['score'] ?? null,

                    'home_record_wins' => $incoming['teams']['home']['leagueRecord']['wins'],
                    'home_record_losses' => $incoming['teams']['home']['leagueRecord']['losses'],
                    'home_score' => $incoming['teams']['home']['score'] ?? null,

                    'type_id' => $type->id ?? null,
                    'status_id' => $status->id ?? null,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'winning_team_id' => $winningTeam->id ?? null,
                    'losing_team_id' => $losingTeam->id ?? null,
                    'venue_id' => $venue->id ?? null,
                ]);
            }
        }
    }
}
