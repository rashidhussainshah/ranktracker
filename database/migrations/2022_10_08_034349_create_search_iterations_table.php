<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchIterationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_iterations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_search_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('search_results')->nullable();
            $table->string('iteration')->default(1);
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
        Schema::dropIfExists('search_iterations');
    }
}
