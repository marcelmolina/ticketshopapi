<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePuestosPalcoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('puestos_palco', function(Blueprint $table)
		{
			$table->bigInteger('id_palco');
			$table->bigInteger('id_puesto')->index('puestos_palco_fk1');
			$table->primary(['id_palco','id_puesto']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('puestos_palco');
	}

}
