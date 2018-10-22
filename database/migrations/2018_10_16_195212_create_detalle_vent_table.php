<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDetalleVentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('detalle_vent', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('id_venta')->index('detalle_venta_fk0');
			$table->bigInteger('id_boleta_evento')->nullable()->unique('id_boleta_evento');
			$table->bigInteger('id_palco_evento')->nullable()->unique('id_palco_evento');
			$table->float('precio_venta', 10, 0);
			$table->float('precio_servicio', 10, 0);
			$table->float('impuesto', 10, 0)->nullable()->default(0);
			$table->boolean('status')->nullable()->default(0);
			$table->bigInteger('id_descuento')->nullable()->index('detalle_venta_fk3');
			$table->float('monto_domicilio', 10, 0)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('detalle_vent');
	}

}
