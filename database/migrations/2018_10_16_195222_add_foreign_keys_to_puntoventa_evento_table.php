<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPuntoventaEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('puntoventa_evento', function(Blueprint $table)
		{
			$table->foreign('id_evento', 'puntoventa_evento_fk0')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_puntoventa', 'puntoventa_evento_fk1')->references('id')->on('punto_venta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('puntoventa_evento', function(Blueprint $table)
		{
			$table->dropForeign('puntoventa_evento_fk0');
			$table->dropForeign('puntoventa_evento_fk1');
		});
	}

}
