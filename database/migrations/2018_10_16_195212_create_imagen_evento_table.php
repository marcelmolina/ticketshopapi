<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagenEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('imagen_evento', function(Blueprint $table)
		{
			$table->bigInteger('id_imagen');
			$table->bigInteger('id_evento')->index('imagen_evento_fk1');
			$table->primary(['id_imagen','id_evento']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('imagen_evento');
	}

}
