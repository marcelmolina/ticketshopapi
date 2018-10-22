<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDevolucionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('devolucion', function(Blueprint $table)
		{
			$table->foreign('email_usuario', 'devolucion_fk0')->references('email')->on('usuario')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_punto_venta', 'devolucion_fk1')->references('id')->on('punto_venta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_venta', 'devolucion_fk2')->references('id_venta')->on('detalle_vent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_detalle_venta', 'devolucion_fk3')->references('id')->on('detalle_vent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_boleta_evento', 'devolucion_fk4')->references('id_boleta_evento')->on('detalle_vent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco_evento', 'devolucion_fk5')->references('id_palco_evento')->on('detalle_vent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('devolucion', function(Blueprint $table)
		{
			$table->dropForeign('devolucion_fk0');
			$table->dropForeign('devolucion_fk1');
			$table->dropForeign('devolucion_fk2');
			$table->dropForeign('devolucion_fk3');
			$table->dropForeign('devolucion_fk4');
			$table->dropForeign('devolucion_fk5');
		});
	}

}
