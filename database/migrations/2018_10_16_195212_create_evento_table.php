<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('evento', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->date('fecha_evento');
			$table->string('nombre', 200);
			$table->time('hora_inicio')->nullable();
			$table->time('hora_apertura')->nullable();
			$table->time('hora_finalizacion')->nullable();
			$table->string('codigo_pulep', 50)->nullable();
			$table->boolean('tipo_evento')->nullable()->default(0);
			$table->boolean('domicilios')->nullable()->default(0);
			$table->boolean('venta_linea')->nullable()->default(1);
			$table->bigInteger('id_auditorio')->index('evento_fk0');
			$table->bigInteger('id_cliente')->index('evento_fk1');
			$table->bigInteger('id_temporada')->nullable()->index('evento_fk2');
			$table->boolean('status')->nullable()->default(0);
			$table->date('fecha_inicio_venta')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('evento');
	}

}
