<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGrupsVendedoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('grups_vendedores', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 100);
			$table->string('caracteristica', 100);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('grups_vendedores');
	}

}
