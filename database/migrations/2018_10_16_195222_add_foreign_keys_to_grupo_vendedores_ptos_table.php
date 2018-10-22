<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGrupoVendedoresPtosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('grupo_vendedores_ptos', function(Blueprint $table)
		{
			$table->foreign('id_grupo_vendedores', 'grupo_vendedores_ptos_fk0')->references('id')->on('grups_vendedores')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_punto_venta', 'grupo_vendedores_ptos_fk1')->references('id')->on('punto_venta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('grupo_vendedores_ptos', function(Blueprint $table)
		{
			$table->dropForeign('grupo_vendedores_ptos_fk0');
			$table->dropForeign('grupo_vendedores_ptos_fk1');
		});
	}

}
