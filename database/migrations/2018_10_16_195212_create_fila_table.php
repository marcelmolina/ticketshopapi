<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFilaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fila', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('id_localidad')->index('fila_fk0');
			$table->string('nombre', 100)->nullable();
			$table->integer('numero')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fila');
	}

}
