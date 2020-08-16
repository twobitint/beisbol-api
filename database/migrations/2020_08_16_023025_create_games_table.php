<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('mlb_id')->unique();
            $table->year('season');
            $table->datetime('datetime');
            $table->datetime('rescheduled_from')->nullable();
            $table->boolean('tie')->nullable();
            $table->unsignedSmallInteger('number');
            $table->boolean('double_header');
            $table->string('mlb_gameday_id')->nullable();
            $table->boolean('tiebreaker');
            $table->string('daynight');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('scheduled_innings');
            $table->unsignedSmallInteger('inning_break_length');
            $table->unsignedSmallInteger('games_in_series');
            $table->unsignedSmallInteger('series_game_number');
            $table->string('series_description');
            $table->string('mlb_record_source');

            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('home_team_id')->nullable();
            $table->unsignedBigInteger('away_team_id')->nullable();
            $table->unsignedBigInteger('venue_id')->nullable();
        });
    }
}
