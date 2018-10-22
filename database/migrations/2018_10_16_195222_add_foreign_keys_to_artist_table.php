<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToArtistTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('artist', function(Blueprint $table)
		{
			$table->foreign('id_genero', 'artista_fk0')->references('id')->on('genero')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('artist', function(Blueprint $table)
		{
			$table->dropForeign('artista_fk0');
		});
	}

}
