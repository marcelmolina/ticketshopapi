<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePalcoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('palco', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 100)->nullable();
			$table->bigInteger('id_localidad')->index('palco_fk0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('palco');
	}

}
