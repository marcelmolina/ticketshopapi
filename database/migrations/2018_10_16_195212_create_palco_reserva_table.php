<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePalcoReservaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('palco_reserva', function(Blueprint $table)
		{
			$table->bigInteger('id_palco_evento')->primary();
			$table->bigInteger('id_preventa')->nullable()->index('palco_reserva_fk1');
			$table->float('abono', 10, 0)->nullable()->default(0);
			$table->float('precio_venta', 10, 0)->nullable()->default(0);
			$table->float('precio_servicio', 10, 0)->nullable()->default(0);
			$table->float('impuesto', 10, 0)->nullable()->default(0);
			$table->float('status', 10, 0)->nullable()->default(0);
			$table->string('email_usuario', 200)->nullable()->index('palco_reserva_fk2');
			$table->bigInteger('id_punto_venta')->nullable()->index('palco_reserva_fk3');
			$table->string('identificacion', 200)->nullable();
			$table->string('razon_nombre', 200)->nullable();
			$table->string('telefono', 200)->nullable();
			$table->string('direccion', 200)->nullable();
			$table->string('email', 200)->nullable();
			$table->string('email_referido', 200)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('palco_reserva');
	}

}
