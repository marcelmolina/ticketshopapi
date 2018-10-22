<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPuestosPalcoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('puestos_palco', function(Blueprint $table)
		{
			$table->foreign('id_palco', 'puestos_palco_fk0')->references('id')->on('palco')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_puesto', 'puestos_palco_fk1')->references('id')->on('puesto')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('puestos_palco', function(Blueprint $table)
		{
			$table->dropForeign('puestos_palco_fk0');
			$table->dropForeign('puestos_palco_fk1');
		});
	}

}
