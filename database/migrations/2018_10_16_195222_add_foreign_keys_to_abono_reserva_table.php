<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAbonoReservaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('abono_reserva', function(Blueprint $table)
		{
			$table->foreign('id_boleto_reserva', 'abono_reserva_fk0')->references('id_boleta')->on('boleta_reserva')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco_reserva', 'abono_reserva_fk1')->references('id_palco_evento')->on('palco_reserva')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('abono_reserva', function(Blueprint $table)
		{
			$table->dropForeign('abono_reserva_fk0');
			$table->dropForeign('abono_reserva_fk1');
		});
	}

}
