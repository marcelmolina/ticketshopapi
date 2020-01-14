<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Vent
 * 
 * @property int $id
 * @property \Carbon\Carbon $fecha
 * @property string $email_usuario
 * @property int $id_punto_venta
 * @property string $tipo_venta
 * @property string $identificacion
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono
 * @property string $email
 * @property bool $tipo_identidicacion
 * 
 * @property \App\Models\Usuario $usuario
 * @property \App\Models\PuntoVentum $punto_ventum
 * @property \Illuminate\Database\Eloquent\Collection $detalle_vents
 *
 * @package App\Models
 */
class Vent extends Eloquent
{
	protected $table = 'vent';
	public $timestamps = false;

	protected $casts = [
		'id_punto_venta' => 'int'
	];

	protected $dates = [
		'fecha'
	];

	protected $fillable = [
		'fecha',
		'token_refventa',
		'email_usuario',
		'id_punto_venta',
		'tipo_venta',
		'identificacion',
		'nombre',
		'direccion',
		'telefono',
		'email',
		'tipo_identidicacion',
		'active'
	];

	public function usuario()
	{
		return $this->belongsTo(\App\Models\Usuario::class, 'email_usuario');
	}

	public function punto_ventum()
	{
		return $this->belongsTo(\App\Models\PuntoVentum::class, 'id_punto_venta');
	}

	public function detalle_pago()
	{
		return $this->belongsTo(\App\Models\Pago::class, 'token_refventa', 'order');
	}

	public function detalle_vents()
	{
		return $this->hasMany(\App\Models\DetalleVent::class, 'id_venta');
	}


}

