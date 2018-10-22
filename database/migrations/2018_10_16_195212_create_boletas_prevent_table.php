<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBoletasPreventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boletas_prevent', function(Blueprint $table)
		{
			$table->bigInteger('id_boleta')->primary();
			$table->bigInteger('id_preventa')->index('boletas_preventa_fk1');
			$table->float('precio_venta', 10, 0);
			$table->float('precio_servicio', 10, 0)->nullable()->default(0);
			$table->float('impuesto', 10, 0)->default(0);
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
		Schema::drop('boletas_prevent');
	}

}
