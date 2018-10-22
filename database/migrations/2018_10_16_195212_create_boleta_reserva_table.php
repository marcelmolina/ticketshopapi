<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBoletaReservaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boleta_reserva', function(Blueprint $table)
		{
			$table->bigInteger('id_boleta')->primary();
			$table->bigInteger('id_preventa')->nullable()->index('boleta_reserva_fk1');
			$table->float('abono', 10, 0)->nullable()->default(0);
			$table->float('precio_venta', 10, 0)->nullable()->default(0);
			$table->float('precio_servicio', 10, 0)->nullable()->default(0);
			$table->float('impuesto', 10, 0)->nullable()->default(0);
			$table->float('status', 10, 0)->nullable()->default(0);
			$table->string('email_usuario', 200)->nullable()->index('boleta_reserva_fk2');
			$table->bigInteger('id_punto_venta')->nullable()->index('boleta_reserva_fk3');
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
		Schema::drop('boleta_reserva');
	}

}
