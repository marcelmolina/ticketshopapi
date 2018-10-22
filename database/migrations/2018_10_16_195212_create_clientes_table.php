<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clientes', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('Identificacion', 50)->unique('Identificacion');
			$table->boolean('tipo_identificacion');
			$table->string('nombrerazon', 200);
			$table->string('direccion');
			$table->string('ciudad', 100)->nullable();
			$table->string('departamento', 100)->nullable();
			$table->boolean('tipo_cliente');
			$table->string('email', 100);
			$table->string('telefono', 50);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clientes');
	}

}
