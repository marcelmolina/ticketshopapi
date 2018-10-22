<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Auditorio
 * 
 * @property int $id
 * @property string $nombre
 * @property string $ciudad
 * @property string $departamento
 * @property string $pais
 * @property string $direccion
 * @property float $latitud
 * @property float $longitud
 * @property int $aforo
 * 
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @property \Illuminate\Database\Eloquent\Collection $imagens
 * @property \Illuminate\Database\Eloquent\Collection $tribunas
 *
 * @package App\Models
 */
class Auditorio extends Eloquent
{
	protected $table = 'auditorio';
	public $timestamps = false;

	protected $casts = [
		'latitud' => 'float',
		'longitud' => 'float',
		'aforo' => 'int'
	];

	protected $fillable = [
		'nombre',
		'ciudad',
		'departamento',
		'pais',
		'direccion',
		'latitud',
		'longitud',
		'aforo'
	];

	public function eventos()
	{
		return $this->hasMany(\App\Models\Evento::class, 'id_auditorio');
	}

	public function imagens()
	{
		return $this->belongsToMany(\App\Models\Imagen::class, 'imagenes_auditorio', 'id_auditorio', 'id_imagen');
	}

	public function tribunas()
	{
		return $this->hasMany(\App\Models\Tribuna::class, 'id_auditorio');
	}
}
