<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocalidadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('localidad', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 200);
			$table->bigInteger('id_tribuna')->index('localidad_fk0');
			$table->string('puerta_acceso', 20)->nullable()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('localidad');
	}

}
