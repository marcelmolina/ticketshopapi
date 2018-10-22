<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDescuentoEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('descuento_evento', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 100);
			$table->bigInteger('id_evento')->index('descuento_evento_fk0');
			$table->dateTime('fecha_hora_inicio')->nullable();
			$table->dateTime('fecha_hora_fin')->nullable();
			$table->boolean('status')->nullable()->default(0);
			$table->bigInteger('tipo_descuento')->nullable()->default(0)->index('descuento_evento_fk1');
			$table->float('porcentaje', 10, 0)->nullable()->default(0);
			$table->float('monto', 10, 0)->nullable()->default(0);
			$table->integer('cantidad_compra')->nullable();
			$table->integer('cantidad_paga')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('descuento_evento');
	}

}
