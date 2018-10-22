<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class GrupsVendedore
 * 
 * @property int $id
 * @property string $nombre
 * @property string $caracteristica
 * 
 * @property \Illuminate\Database\Eloquent\Collection $grupo_vendedores_ptos
 *
 * @package App\Models
 */
class GrupsVendedore extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'nombre',
		'caracteristica'
	];

	public function grupo_vendedores_ptos()
	{
		return $this->hasMany(\App\Models\GrupoVendedoresPto::class, 'id_grupo_vendedores');
	}
}
