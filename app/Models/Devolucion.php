<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Devolucion
 * 
 * @property int $id
 * @property \Carbon\Carbon $fecha
 * @property string $email_usuario
 * @property int $id_punto_venta
 * @property string $identificacion
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono
 * @property string $email
 * @property bool $tipo_identificacion
 * @property int $id_venta
 * @property int $id_detalle_venta
 * @property int $id_boleta_evento
 * @property int $id_palco_evento
 * 
 * @property \App\Models\Usuario $usuario
 * @property \App\Models\PuntoVentum $punto_ventum
 * @property \App\Models\DetalleVent $detalle_vent
 *
 * @package App\Models
 */
class Devolucion extends Eloquent
{
	protected $table = 'devolucion';
	public $timestamps = false;

	protected $casts = [
		'id_punto_venta' => 'int',
		'tipo_identificacion' => 'bool',
		'id_venta' => 'int',
		'id_detalle_venta' => 'int',
		'id_boleta_evento' => 'int',
		'id_palco_evento' => 'int'
	];

	protected $dates = [
		'fecha'
	];

	protected $fillable = [
		'fecha',
		'email_usuario',
		'id_punto_venta',
		'identificacion',
		'nombre',
		'direccion',
		'telefono',
		'email',
		'tipo_identificacion',
		'id_venta',
		'id_detalle_venta',
		'id_boleta_evento',
		'id_palco_evento'
	];

	public function usuario()
	{
		return $this->belongsTo(\App\Models\Usuario::class, 'email_usuario');
	}

	public function punto_ventum()
	{
		return $this->belongsTo(\App\Models\PuntoVentum::class, 'id_punto_venta');
	}

	public function detalle_vent()
	{
		return $this->belongsTo(\App\Models\DetalleVent::class, 'id_palco_evento', 'id_palco_evento');
	}
}
