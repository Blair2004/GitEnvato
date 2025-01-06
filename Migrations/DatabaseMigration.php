<?php
/**
 * Table Migration
 * @package 5.3.3
**/

namespace Modules\GitEnvato\Migrations;

use App\Classes\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::createIfMissing( 'gitenvato_repositories', function( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->string( 'name' );
            $table->boolean( 'active' )->default( true );
            $table->string( 'branch' )->nullable();
            $table->boolean( 'publish_betas' )->default( false );
            $table->text( 'description' )->nullable();
            $table->integer( 'author' );
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
        Schema::dropIfExists( 'gitenvato_repositories' );
    }
};
