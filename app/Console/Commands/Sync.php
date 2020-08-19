<?php

namespace App\Console\Commands;

use App\Division;
use App\Game;
use App\GameStatus;
use App\GameType;
use App\League;
use App\RosterType;
use App\Sport;
use App\Venue;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local data with MLB API';

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
        $this->scaffolding();
        $this->data();
    }

    protected function scaffolding()
    {
        GameType::sync();
        RosterType::sync();
        GameStatus::sync();
        // Sport::sync();
        // League::sync();
        // Division::sync();
        // Venue::sync();
    }

    protected function data()
    {
        Game::sync();
    }
}
