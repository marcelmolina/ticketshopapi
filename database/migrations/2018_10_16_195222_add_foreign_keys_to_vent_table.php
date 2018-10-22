<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vent', function(Blueprint $table)
		{
			$table->foreign('email_usuario', 'venta_fk0')->references('email')->on('usuario')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_punto_venta', 'venta_fk1')->references('id')->on('punto_venta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vent', function(Blueprint $table)
		{
			$table->dropForeign('venta_fk0');
			$table->dropForeign('venta_fk1');
		});
	}

}
