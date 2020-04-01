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
 * @property string $code
 * @property string $descripcion
 * 
 * @property \Illuminate\Database\Eloquent\Collection $usuarios
 *
 * @package App\Models
 */

class TipoIdentificacion extends Eloquent
{
	protected $table = 'tipo_identificacion';
	public $timestamps = false;

	protected $fillable = [
		'code',
		'descripcion'
	];

	public function usuarios()
	{
		return $this->hasMany(\App\Models\Usuario::class, 'id');
	}
}
