<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagenesAuditorioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('imagenes_auditorio', function(Blueprint $table)
		{
			$table->bigInteger('id_imagen');
			$table->bigInteger('id_auditorio')->index('imagenes_auditorio_fk1');
			$table->primary(['id_imagen','id_auditorio']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('imagenes_auditorio');
	}

}
