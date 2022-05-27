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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('upline')->default(0);
            $table->boolean('leader')->default(0);
            $table->enum('status', [1, 0, -1])->default(0);
            $table->boolean('second_factor')->default(0);
            $table->string('language', 2)->default('en');
            $table->foreignId('main_wallet')->default(1);
            $table->boolean('administrator')->default(0);
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
        Schema::dropIfExists('users');
    }
};
