<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            //
            // Fields
            //

            // Unique ID
            $table->increments('id');
            $table->string('uri'. 250)->index()->comment('Url searched');
            $table->string('title')->nullable()->index()->comment('Site Title');
            $table->boolean('success')->default(false)->comment('Search successful');
            $table->string('wordpress_version')->nullable()->comment('Wordpress Version');
            $table->string('ip')->comment('User IP for Geolocation');
            $table->integer('main_theme_id')->unsigned()->nullable()->comment('Main Theme Id');
            $table->integer('child_theme_id')->unsigned()->nullable()->comment('Child Theme Id');
            $table->longText('error')->nullable()->comment('Error');
            // Laravel created_at
            $table->timestamp('created_at')->useCurrent()->comment('Created At');

            //
            // Foreign keys
            //
            $table->foreign('main_theme_id')->references('id')->on('themes')->nullable();
            $table->foreign('child_theme_id')->references('id')->on('themes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
}
