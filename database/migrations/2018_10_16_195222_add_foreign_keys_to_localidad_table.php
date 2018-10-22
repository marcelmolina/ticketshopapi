<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLocalidadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('localidad', function(Blueprint $table)
		{
			$table->foreign('id_tribuna', 'localidad_fk0')->references('id')->on('tribuna')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('localidad', function(Blueprint $table)
		{
			$table->dropForeign('localidad_fk0');
		});
	}

}
