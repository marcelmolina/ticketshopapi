<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDetalleVentaTemporadaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('detalle_venta_temporada', function(Blueprint $table)
		{
			$table->foreign('id_venta_temporada', 'detalle_venta_temporada_fk0')->references('id')->on('venta_temporada')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_evento', 'detalle_venta_temporada_fk1')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_boleta_evento', 'detalle_venta_temporada_fk2')->references('id')->on('boleta_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco_evento', 'detalle_venta_temporada_fk3')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('detalle_venta_temporada', function(Blueprint $table)
		{
			$table->dropForeign('detalle_venta_temporada_fk0');
			$table->dropForeign('detalle_venta_temporada_fk1');
			$table->dropForeign('detalle_venta_temporada_fk2');
			$table->dropForeign('detalle_venta_temporada_fk3');
		});
	}

}
