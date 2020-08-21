<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRosterStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('code');
            $table->string('description')->nullable();
        });
    }
}
