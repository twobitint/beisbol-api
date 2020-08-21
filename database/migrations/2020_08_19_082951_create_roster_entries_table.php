<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRosterEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->date('start');
            $table->date('end')->nullable();
            $table->unsignedBigInteger('roster_type_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('roster_status_id')->nullable();
        });
    }
}
