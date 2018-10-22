<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDevolucionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('devolucion', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->date('fecha');
			$table->string('email_usuario', 200)->nullable()->index('devolucion_fk0');
			$table->bigInteger('id_punto_venta')->nullable()->index('devolucion_fk1');
			$table->string('identificacion', 200)->nullable();
			$table->string('nombre', 200)->nullable();
			$table->string('direccion', 200)->nullable();
			$table->string('telefono', 200)->nullable();
			$table->string('email', 200)->nullable();
			$table->boolean('tipo_identificacion')->nullable()->default(0);
			$table->bigInteger('id_venta')->index('devolucion_fk2');
			$table->bigInteger('id_detalle_venta')->index('devolucion_fk3');
			$table->bigInteger('id_boleta_evento')->nullable()->index('devolucion_fk4');
			$table->bigInteger('id_palco_evento')->nullable()->index('devolucion_fk5');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('devolucion');
	}

}
