<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBoletaEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boleta_evento', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('id_evento')->index('boleta_evento_fk0');
			$table->bigInteger('id_puesto')->index('boleta_evento_fk1');
			$table->float('precio_venta', 10, 0)->default(0);
			$table->float('precio_servicio', 10, 0)->default(0);
			$table->float('impuesto', 10, 0)->nullable()->default(0);
			$table->boolean('status')->nullable()->default(0);
			$table->string('codigo')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('boleta_evento');
	}

}
