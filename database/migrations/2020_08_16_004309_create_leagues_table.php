<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('mlb_id')->unique();
            $table->string('name');
            $table->string('abbrev');

            $table->unsignedBigInteger('sport_id')->nullable();
        });
    }
}
