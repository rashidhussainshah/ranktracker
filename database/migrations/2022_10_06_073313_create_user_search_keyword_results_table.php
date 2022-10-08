<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSearchKeywordResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_search_keyword_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_search_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type',10)->default("organic");
            $table->string('rank_group')->nullable();
            $table->string('rank_absolute')->nullable();
            $table->string('domain')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('breadcrumb')->nullable();
            $table->json('factors_data')->nullable();
            $table->json('nlp_entities')->nullable();
            $table->text('page_html_data')->nullable();
            $table->integer('referring_domains')->default(0);
            $table->integer('organic_traffic')->default(0);
            $table->integer('paid_traffic')->default(0);
            $table->integer('back_links')->default(0);
            $table->integer('backlink_page_no')->default(0);
            $table->integer('live_back_links')->default(0);
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
        Schema::dropIfExists('user_search_keyword_results');
    }
}
