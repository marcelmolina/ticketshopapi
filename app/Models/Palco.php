<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Palco
 * 
 * @property int $id
 * @property string $nombre
 * @property int $id_localidad
 * 
 * @property \App\Models\Localidad $localidad
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @property \Illuminate\Database\Eloquent\Collection $puestos
 *
 * @package App\Models
 */
class Palco extends Eloquent
{
	protected $table = 'palco';
	public $timestamps = false;

	protected $casts = [
		'id_localidad' => 'int'
	];

	protected $fillable = [
		'nombre',
		'id_localidad'
	];

	public function localidad()
	{
		return $this->belongsTo(\App\Models\Localidad::class, 'id_localidad');
	}

	public function eventos()
	{
		return $this->belongsToMany(\App\Models\Evento::class, 'palco_evento', 'id_palco', 'id_evento')
					->withPivot('id', 'precio_venta', 'precio_servicio', 'impuesto', 'status');
	}

	public function puestos()
	{
		return $this->belongsToMany(\App\Models\Puesto::class, 'puestos_palco', 'id_palco', 'id_puesto');
	}
}
