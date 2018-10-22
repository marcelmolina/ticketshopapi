<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Rol
 * 
 * @property int $id
 * @property string $nombre
 * 
 * @property \Illuminate\Database\Eloquent\Collection $usuarios
 *
 * @package App\Models
 */
class Rol extends Eloquent
{
	protected $table = 'rol';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function usuarios()
	{
		return $this->hasMany(\App\Models\Usuario::class, 'id_rol');
	}
}
