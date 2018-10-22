<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToImagenesAuditorioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('imagenes_auditorio', function(Blueprint $table)
		{
			$table->foreign('id_imagen', 'imagenes_auditorio_fk0')->references('id')->on('imagen')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_auditorio', 'imagenes_auditorio_fk1')->references('id')->on('auditorio')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('imagenes_auditorio', function(Blueprint $table)
		{
			$table->dropForeign('imagenes_auditorio_fk0');
			$table->dropForeign('imagenes_auditorio_fk1');
		});
	}

}
