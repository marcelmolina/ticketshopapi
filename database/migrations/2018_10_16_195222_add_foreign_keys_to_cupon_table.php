<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCuponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cupon', function(Blueprint $table)
		{
			$table->foreign('id_tipo_cupon', 'cupon_fk0')->references('id')->on('tipo_cupon')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_cuponera', 'cupon_fk1')->references('id')->on('cuponera')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cupon', function(Blueprint $table)
		{
			$table->dropForeign('cupon_fk0');
			$table->dropForeign('cupon_fk1');
		});
	}

}
