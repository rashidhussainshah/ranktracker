<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('keyword');
            $table->string('country');
            $table->enum('status', ['processing','complete','error']);
            $table->enum('device', ['desktop', 'mobile'])->default('desktop');
            $table->integer('search_repetitions')->default(0)->comment('how many times will the search be repeated');
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
        Schema::dropIfExists('user_searches');
    }
}
