<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unistream_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unistream_rates');
    }
};