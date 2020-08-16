<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('mlb_id')->unique();
            $table->string('mlb_file_code')->unique();
            $table->string('name');
            $table->string('code');
            $table->string('abbrev');
            $table->string('location');
            $table->year('first_played');

            $table->unsignedBigInteger('venue_id')->nullable();
            $table->unsignedBigInteger('league_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('sport_id')->nullable();
            $table->unsignedBigInteger('parent_team_id')->nullable();
        });
    }
}
