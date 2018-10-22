<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGrupoVendedoresPtosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('grupo_vendedores_ptos', function(Blueprint $table)
		{
			$table->bigInteger('id_grupo_vendedores');
			$table->bigInteger('id_punto_venta')->index('grupo_vendedores_ptos_fk1');
			$table->primary(['id_grupo_vendedores','id_punto_venta']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('grupo_vendedores_ptos');
	}

}
