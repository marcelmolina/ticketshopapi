<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePreventaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('preventa', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 200);
			$table->bigInteger('id_evento')->index('preventa_fk0');
			$table->dateTime('fecha_inicio')->nullable();
			$table->dateTime('fecha_fin')->nullable();
			$table->boolean('activo')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('preventa');
	}

}
