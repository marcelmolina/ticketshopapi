<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDetalleDescuentoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('detalle_descuento', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('id_descuento')->index('detalle_descuento_fk0');
			$table->bigInteger('id_tribuna')->nullable()->index('detalle_descuento_fk1');
			$table->bigInteger('id_localidad')->nullable()->index('detalle_descuento_fk2');
			$table->bigInteger('id_boleta_evento')->nullable()->index('detalle_descuento_fk3');
			$table->bigInteger('id_palco_evento')->nullable()->index('detalle_descuento_fk4');
			$table->float('porcentaje', 10, 0)->nullable()->default(0);
			$table->float('monto', 10, 0)->nullable()->default(0);
			$table->float('status', 10, 0)->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('detalle_descuento');
	}

}
