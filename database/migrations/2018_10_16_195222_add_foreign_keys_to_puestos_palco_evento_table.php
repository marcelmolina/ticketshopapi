<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPuestosPalcoEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('puestos_palco_evento', function(Blueprint $table)
		{
			$table->foreign('id_palco_evento', 'puestos_palco_evento_fk0')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco', 'puestos_palco_evento_fk1')->references('id_palco')->on('puestos_palco')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_puesto', 'puestos_palco_evento_fk2')->references('id_puesto')->on('puestos_palco')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('puestos_palco_evento', function(Blueprint $table)
		{
			$table->dropForeign('puestos_palco_evento_fk0');
			$table->dropForeign('puestos_palco_evento_fk1');
			$table->dropForeign('puestos_palco_evento_fk2');
		});
	}

}
