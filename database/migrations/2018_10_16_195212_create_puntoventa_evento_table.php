<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePuntoventaEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('puntoventa_evento', function(Blueprint $table)
		{
			$table->bigInteger('id_evento');
			$table->bigInteger('id_puntoventa')->index('puntoventa_evento_fk1');
			$table->primary(['id_evento','id_puntoventa']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('puntoventa_evento');
	}

}
