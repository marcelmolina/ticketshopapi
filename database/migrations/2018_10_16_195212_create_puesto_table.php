<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePuestoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('puesto', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('numero', 20)->nullable();
			$table->bigInteger('id_localidad')->index('puesto_fk0');
			$table->bigInteger('id_fila')->nullable()->index('puesto_fk1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('puesto');
	}

}
