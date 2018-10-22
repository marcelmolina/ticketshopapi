<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBoletaReservaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boleta_reserva', function(Blueprint $table)
		{
			$table->foreign('id_boleta', 'boleta_reserva_fk0')->references('id')->on('boleta_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_preventa', 'boleta_reserva_fk1')->references('id')->on('preventa')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('email_usuario', 'boleta_reserva_fk2')->references('email')->on('usuario')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_punto_venta', 'boleta_reserva_fk3')->references('id')->on('punto_venta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('boleta_reserva', function(Blueprint $table)
		{
			$table->dropForeign('boleta_reserva_fk0');
			$table->dropForeign('boleta_reserva_fk1');
			$table->dropForeign('boleta_reserva_fk2');
			$table->dropForeign('boleta_reserva_fk3');
		});
	}

}
