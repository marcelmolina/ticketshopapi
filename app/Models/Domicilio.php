<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Domicilio
 * 
 * @property int $id
 * @property \Carbon\Carbon $fecha_hora_entrega
 * @property int $id_detalle_venta
 * @property string $nombre_recibe
 * @property string $direcion
 * @property string $ciudad
 * @property string $telefono
 * @property string $email
 * @property bool $status
 * 
 * @property \App\Models\DetalleVent $detalle_vent
 *
 * @package App\Models
 */
class Domicilio extends Eloquent
{
	protected $table = 'domicilio';
	public $timestamps = false;

	protected $casts = [
		'id_detalle_venta' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'fecha_hora_entrega'
	];

	protected $fillable = [
		'fecha_hora_entrega',
		'id_detalle_venta',
		'nombre_recibe',
		'direcion',
		'ciudad',
		'telefono',
		'email',
		'status'
	];

	public function detalle_vent()
	{
		return $this->belongsTo(\App\Models\DetalleVent::class, 'id_detalle_venta');
	}
}
