<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Yahoo\API;

class Yahoo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yahoo {--teams} {--draft} {--leagues} {--uri=} {--league=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use the Yahoo Sports API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = env('YAHOO_USER_TOKEN');

        $league = $this->option('league') ?? env('YAHOO_MLB_LEAGUE_KEY');

        if ($this->option('leagues')) {
            dd(API::request($token, '/users;use_login=1/games;game_keys=' . env('YAHOO_MLB_GAME_KEY') . '/leagues'));
        }

        if ($this->option('teams')) {
            dd(API::request($token, '/league/' . $league . '/teams'));
        }

        // This works during draft.
        if ($this->option('draft')) {
            dd(API::request($token, '/league/' . $league . '/draftresults/players'));
        }

        $uri = '/' . $this->option('uri');

        dd(API::request($token, $uri));
    }
}
