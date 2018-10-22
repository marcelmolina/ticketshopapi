<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToImagenArtistTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('imagen_artist', function(Blueprint $table)
		{
			$table->foreign('id_artista', 'imagen_artista_fk0')->references('id')->on('artist')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_imagen', 'imagen_artista_fk1')->references('id')->on('imagen')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('imagen_artist', function(Blueprint $table)
		{
			$table->dropForeign('imagen_artista_fk0');
			$table->dropForeign('imagen_artista_fk1');
		});
	}

}
