<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToArtistaEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('artista_evento', function(Blueprint $table)
		{
			$table->foreign('id_artista', 'artista_evento_fk0')->references('id')->on('artist')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_evento', 'artista_evento_fk1')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('artista_evento', function(Blueprint $table)
		{
			$table->dropForeign('artista_evento_fk0');
			$table->dropForeign('artista_evento_fk1');
		});
	}

}
