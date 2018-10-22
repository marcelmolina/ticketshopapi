<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDomicilioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('domicilio', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->dateTime('fecha_hora_entrega')->nullable();
			$table->bigInteger('id_detalle_venta')->index('domicilio_fk0');
			$table->string('nombre_recibe', 200)->nullable();
			$table->string('direcion', 200)->nullable();
			$table->string('ciudad', 200)->nullable();
			$table->string('telefono', 200)->nullable();
			$table->string('email', 200)->nullable();
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
		Schema::drop('domicilio');
	}

}
