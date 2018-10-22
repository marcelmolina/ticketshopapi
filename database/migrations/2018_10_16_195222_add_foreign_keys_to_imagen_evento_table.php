<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToImagenEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('imagen_evento', function(Blueprint $table)
		{
			$table->foreign('id_imagen', 'imagen_evento_fk0')->references('id')->on('imagen')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_evento', 'imagen_evento_fk1')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('imagen_evento', function(Blueprint $table)
		{
			$table->dropForeign('imagen_evento_fk0');
			$table->dropForeign('imagen_evento_fk1');
		});
	}

}
