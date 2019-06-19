<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class GrupoVendedoresPto
 * 
 * @property int $id_grupo_vendedores
 * @property int $id_punto_venta
 * 
 * @property \App\Models\GrupsVendedore $grups_vendedore
 * @property \App\Models\PuntoVentum $punto_ventum
 *
 * @package App\Models
 */
class GrupoVendedoresPto extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_grupo_vendedores' => 'int',
		'id_punto_venta' => 'int'
	];

	protected $fillable = [
		'id_grupo_vendedores', 
		'id_punto_venta'
	];

	public function grups_vendedore()
	{
		return $this->belongsTo(\App\Models\GrupsVendedore::class, 'id_grupo_vendedores');
	}

	public function punto_ventum()
	{
		return $this->belongsTo(\App\Models\PuntoVentum::class, 'id_punto_venta');
	}
}
