<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Fila
 * 
 * @property int $id
 * @property int $id_localidad
 * @property string $nombre
 * @property int $numero
 * 
 * @property \App\Models\Localidad $localidad
 * @property \Illuminate\Database\Eloquent\Collection $puestos
 *
 * @package App\Models
 */
class Fila extends Eloquent
{
	protected $table = 'fila';
	public $timestamps = false;

	protected $casts = [
		'id_localidad' => 'int',
		'numero' => 'int'
	];

	protected $fillable = [
		'id_localidad',
		'nombre',
		'numero'
	];

	public function localidad()
	{
		return $this->belongsTo(\App\Models\Localidad::class, 'id_localidad');
	}

	public function puestos()
	{
		return $this->hasMany(\App\Models\Puesto::class, 'id_fila');
	}
}
