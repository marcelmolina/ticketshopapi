<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDetalleVentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('detalle_vent', function(Blueprint $table)
		{
			$table->foreign('id_venta', 'detalle_venta_fk0')->references('id')->on('vent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_boleta_evento', 'detalle_venta_fk1')->references('id')->on('boleta_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco_evento', 'detalle_venta_fk2')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_descuento', 'detalle_venta_fk3')->references('id')->on('descuento_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('detalle_vent', function(Blueprint $table)
		{
			$table->dropForeign('detalle_venta_fk0');
			$table->dropForeign('detalle_venta_fk1');
			$table->dropForeign('detalle_venta_fk2');
			$table->dropForeign('detalle_venta_fk3');
		});
	}

}
