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
 * @property int $id_ciudad
 * @property int $id_departamento
 * @property int $id_pais
 * @property string $direccion
 * @property float $latitud
 * @property float $longitud
 * @property int $aforo
 * @property string $url_imagen
 * @property string $codigo_mapeado
 * 
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @property \Illuminate\Database\Eloquent\Collection $ciudad
 * @property \Illuminate\Database\Eloquent\Collection $departamento
 * @property \Illuminate\Database\Eloquent\Collection $pais
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
		'id_ciudad',
		'id_departamento',
		'id_pais',
		'direccion',
		'latitud',
		'longitud',
		'aforo',
		'url_imagen',
		'codigo_mapeado'
	];

	public function eventos()
	{
		return $this->hasMany(\App\Models\Evento::class, 'id_auditorio');
	}

	public function ciudad()
	{
		return $this->belongsTo(\App\Models\Ciudad::class, 'id_ciudad');
	}

	public function departamento()
	{
		return $this->belongsTo(\App\Models\Departamento::class, 'id_departamento');
	}

	public function pais()
	{
		return $this->belongsTo(\App\Models\Pais::class, 'id_pais');
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
