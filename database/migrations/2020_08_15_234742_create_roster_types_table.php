<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRosterTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('mlb_id')->unique();
            $table->string('description');
        });
    }
}
