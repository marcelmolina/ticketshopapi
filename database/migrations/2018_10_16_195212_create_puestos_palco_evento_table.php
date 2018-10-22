<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePuestosPalcoEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('puestos_palco_evento', function(Blueprint $table)
		{
			$table->bigInteger('id_palco_evento');
			$table->bigInteger('id_palco')->index('puestos_palco_evento_fk1');
			$table->bigInteger('id_puesto')->index('puestos_palco_evento_fk2');
			$table->primary(['id_palco_evento','id_palco','id_puesto']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('puestos_palco_evento');
	}

}
