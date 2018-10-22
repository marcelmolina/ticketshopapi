<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDetalleVentaTemporadaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('detalle_venta_temporada', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('id_venta_temporada')->index('detalle_venta_temporada_fk0');
			$table->bigInteger('id_evento')->index('detalle_venta_temporada_fk1');
			$table->bigInteger('id_boleta_evento')->nullable()->unique('id_boleta_evento');
			$table->bigInteger('id_palco_evento')->nullable()->unique('id_palco_evento');
			$table->boolean('status')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('detalle_venta_temporada');
	}

}
