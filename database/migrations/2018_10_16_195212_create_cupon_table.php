<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCuponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cupon', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('codigo')->nullable();
			$table->boolean('status')->default(0);
			$table->float('monto', 10, 0)->nullable()->default(0);
			$table->float('porcentaje_descuento', 10, 0)->nullable()->default(0);
			$table->bigInteger('id_tipo_cupon')->index('cupon_fk0');
			$table->bigInteger('id_cuponera')->index('cupon_fk1');
			$table->integer('cantidad_compra')->nullable();
			$table->integer('cantidad_paga')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cupon');
	}

}
