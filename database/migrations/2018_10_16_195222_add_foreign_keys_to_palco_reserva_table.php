<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPalcoReservaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('palco_reserva', function(Blueprint $table)
		{
			$table->foreign('id_palco_evento', 'palco_reserva_fk0')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_preventa', 'palco_reserva_fk1')->references('id')->on('preventa')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('email_usuario', 'palco_reserva_fk2')->references('email')->on('usuario')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_punto_venta', 'palco_reserva_fk3')->references('id')->on('punto_venta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('palco_reserva', function(Blueprint $table)
		{
			$table->dropForeign('palco_reserva_fk0');
			$table->dropForeign('palco_reserva_fk1');
			$table->dropForeign('palco_reserva_fk2');
			$table->dropForeign('palco_reserva_fk3');
		});
	}

}
