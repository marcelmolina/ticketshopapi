<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuditorioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('auditorio', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 200);
			$table->string('ciudad', 200);
			$table->string('departamento', 200);
			$table->string('pais', 200);
			$table->string('direccion', 200);
			$table->float('latitud', 10, 0)->nullable();
			$table->float('longitud', 10, 0)->nullable();
			$table->integer('aforo')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('auditorio');
	}

}
