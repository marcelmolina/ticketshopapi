<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArtistaEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('artista_evento', function(Blueprint $table)
		{
			$table->bigInteger('id_artista');
			$table->bigInteger('id_evento')->index('artista_evento_fk1');
			$table->primary(['id_artista','id_evento']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('artista_evento');
	}

}
