<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTribunaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tribuna', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 200);
			$table->bigInteger('id_auditorio')->index('tribuna_fk0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tribuna');
	}

}
