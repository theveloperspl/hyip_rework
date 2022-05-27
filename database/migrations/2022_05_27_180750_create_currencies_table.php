<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('chain')->nullable();
            $table->string('big')->nullable();
            $table->string('long');
            $table->bigInteger('withdrawal');
            $table->string('converted_withdrawal');
            $table->enum('type', ['crypto', 'fiat']);
            $table->enum('mode', ['manual', 'automatic']);
            $table->char('validator');
            $table->enum('destination_tag', [1, 0])->default(0);
            $table->enum('enabled', [1, 0, -1])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};
