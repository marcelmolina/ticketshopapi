<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vent', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->date('fecha');
			$table->string('email_usuario', 200)->nullable()->index('venta_fk0');
			$table->bigInteger('id_punto_venta')->nullable()->index('venta_fk1');
			$table->string('tipo_venta', 10)->nullable();
			$table->string('identificacion', 200)->nullable();
			$table->string('nombre', 200)->nullable();
			$table->string('direccion', 200)->nullable();
			$table->string('telefono', 200)->nullable();
			$table->string('email', 200)->nullable();
			$table->boolean('tipo_identidicacion')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vent');
	}

}
