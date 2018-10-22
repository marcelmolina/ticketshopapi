<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventoCuponeraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('evento_cuponera', function(Blueprint $table)
		{
			$table->bigInteger('id_evento');
			$table->bigInteger('id_cuponera')->index('evento_cuponera_fk1');
			$table->primary(['id_evento','id_cuponera']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('evento_cuponera');
	}

}
