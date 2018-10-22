<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAbonoReservaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('abono_reserva', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('id_boleto_reserva')->nullable()->index('abono_reserva_fk0');
			$table->bigInteger('id_palco_reserva')->nullable()->index('abono_reserva_fk1');
			$table->float('monto_abono', 10, 0)->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('abono_reserva');
	}

}
