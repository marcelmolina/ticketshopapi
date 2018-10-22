<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePalcoPreventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('palco_prevent', function(Blueprint $table)
		{
			$table->bigInteger('id_palco_evento')->primary();
			$table->bigInteger('id_preventa')->index('palco_preventa_fk1');
			$table->float('precio_venta', 10, 0)->nullable()->default(0);
			$table->float('precio_servicio', 10, 0)->nullable()->default(0);
			$table->float('impuesto', 10, 0)->nullable()->default(0);
			$table->string('status', 10)->nullable()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('palco_prevent');
	}

}
