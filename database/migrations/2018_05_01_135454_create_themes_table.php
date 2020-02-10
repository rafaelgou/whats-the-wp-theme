<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            //
            // Fields
            // These fields mimetize Theme Info from styles.css
            // @https://codex.wordpress.org/Theme_Development#Theme_Stylesheet
            //

            // Unique ID
            $table->increments('id');

            $table->string('name', 150)->index()->comment('Theme Name');
            $table->string('type')->comment('Type main/child');
            $table->string('uri')->nullable()->comment('ThemeURI');
            $table->longText('description')->nullable()->comment('Description');
            $table->string('author')->nullable()->comment('Author');
            $table->string('author_uri')->nullable()->comment('Author URI');
            $table->string('version')->nullable()->comment('Version');
            $table->string('template')->nullable()->comment('Template');
            $table->string('status')->nullable()->comment('Status');
            $table->string('license')->nullable()->comment('License');
            $table->string('license_uri')->nullable()->comment('License URI');
            $table->string('tags')->nullable()->comment('Tags');
            $table->string('text_domain')->nullable()->comment('Text Domain');
            $table->string('domain_path')->nullable()->comment('Domain Path');
            $table->string('theme_id')->nullable()->comment('Theme Id');
            $table->string('style_uri')->nullable()->comment('Style.css URL');
            $table->string('screenshot_uri')->nullable()->comment('Screnshot URL');
            $table->integer('parent_id')->unsigned()->nullable()->comment('Parent theme ID');

            // Laravel created_at
            $table->timestamp('created_at')->useCurrent()->comment('Created At');

            //
            // Foreign keys / Indexes
            //
            // $table->unique('name', 'version'); // Makes sense?
            $table->foreign('parent_id')->references('id')->on('themes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
}
