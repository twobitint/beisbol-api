<?php

namespace App\Console\Commands;

use App\MLB\API;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MLB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mlb {mode} {--from=} {--to=} {--type=} {ids*}';

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
        $api = API::get();

        if ($from = $this->option('from')) {
            $api->from(new Carbon($from));
        }
        if ($to = $this->option('to')) {
            $api->to(new Carbon($to));
        }
        if ($type = $this->option('type')) {
            $api->type($type);
        }

        $ids = $this->argument('ids');
        $id = $ids[0];

        switch ($this->argument('mode')) {
            case 'games':
                dd($api->games());
                break;
            case 'game':
                dd($api->game($id));
                break;
            case 'roster':
                dd($api->roster($id));
                break;
            case 'teams':
                dd($api->teams());
                break;
        }
    }
}
