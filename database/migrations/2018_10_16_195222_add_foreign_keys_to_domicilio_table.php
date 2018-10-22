<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDomicilioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('domicilio', function(Blueprint $table)
		{
			$table->foreign('id_detalle_venta', 'domicilio_fk0')->references('id')->on('detalle_vent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('domicilio', function(Blueprint $table)
		{
			$table->dropForeign('domicilio_fk0');
		});
	}

}
