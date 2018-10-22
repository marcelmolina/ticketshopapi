<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagenArtistTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('imagen_artist', function(Blueprint $table)
		{
			$table->bigInteger('id_artista');
			$table->bigInteger('id_imagen')->index('imagen_artista_fk1');
			$table->primary(['id_artista','id_imagen']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('imagen_artist');
	}

}
