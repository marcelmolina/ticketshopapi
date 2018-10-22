<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDetalleDescuentoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('detalle_descuento', function(Blueprint $table)
		{
			$table->foreign('id_descuento', 'detalle_descuento_fk0')->references('id')->on('descuento_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_tribuna', 'detalle_descuento_fk1')->references('id')->on('tribuna')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_localidad', 'detalle_descuento_fk2')->references('id')->on('localidad')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_boleta_evento', 'detalle_descuento_fk3')->references('id')->on('boleta_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco_evento', 'detalle_descuento_fk4')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('detalle_descuento', function(Blueprint $table)
		{
			$table->dropForeign('detalle_descuento_fk0');
			$table->dropForeign('detalle_descuento_fk1');
			$table->dropForeign('detalle_descuento_fk2');
			$table->dropForeign('detalle_descuento_fk3');
			$table->dropForeign('detalle_descuento_fk4');
		});
	}

}
