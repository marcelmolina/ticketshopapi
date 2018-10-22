<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TipoDescuento
 * 
 * @property int $id
 * @property string $nombre
 * 
 * @property \Illuminate\Database\Eloquent\Collection $descuento_eventos
 *
 * @package App\Models
 */
class TipoDescuento extends Eloquent
{
	protected $table = 'tipo_descuento';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function descuento_eventos()
	{
		return $this->hasMany(\App\Models\DescuentoEvento::class, 'tipo_descuento');
	}
}
