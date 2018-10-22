<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePuntoVentaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('punto_venta', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre_razon', 200);
			$table->string('identificacion', 200);
			$table->string('direccion', 200)->nullable();
			$table->string('telefono', 200)->nullable();
			$table->boolean('tipo_identificacion');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('punto_venta');
	}

}
