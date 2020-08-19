<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('mlb_id')->unique();
            $table->string('description');
            $table->string('game_code');
            $table->string('abstract_description');
            $table->string('abstract_code');
            $table->string('reason')->nullable();
        });
    }
}
