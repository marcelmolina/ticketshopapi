<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Usuario
 * 
 * @property string $email
 * @property string $nombre
 * @property boolean $clave
 * @property string $identificacion
 * @property bool $tipo_identificacion
 * @property string $direccion
 * @property string $ciudad
 * @property string $departamento
 * @property string $telefono
 * @property int $id_rol
 * 
 * @property \App\Models\Rol $rol
 * @property \Illuminate\Database\Eloquent\Collection $boleta_reservas
 * @property \Illuminate\Database\Eloquent\Collection $devolucions
 * @property \Illuminate\Database\Eloquent\Collection $palco_reservas
 * @property \Illuminate\Database\Eloquent\Collection $vents
 * @property \Illuminate\Database\Eloquent\Collection $venta_temporadas
 *
 * @package App\Models
 */
class Usuario extends Eloquent
{
	protected $table = 'usuario';
	protected $primaryKey = 'email';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'clave' => 'boolean',
		'tipo_identificacion' => 'bool',
		'id_rol' => 'int'
	];

	protected $fillable = [
		'nombre',
		'clave',
		'identificacion',
		'tipo_identificacion',
		'direccion',
		'ciudad',
		'departamento',
		'telefono',
		'id_rol'
	];

	public function rol()
	{
		return $this->belongsTo(\App\Models\Rol::class, 'id_rol');
	}

	public function boleta_reservas()
	{
		return $this->hasMany(\App\Models\BoletaReserva::class, 'email_usuario');
	}

	public function devolucions()
	{
		return $this->hasMany(\App\Models\Devolucion::class, 'email_usuario');
	}

	public function palco_reservas()
	{
		return $this->hasMany(\App\Models\PalcoReserva::class, 'email_usuario');
	}

	public function vents()
	{
		return $this->hasMany(\App\Models\Vent::class, 'email_usuario');
	}

	public function venta_temporadas()
	{
		return $this->hasMany(\App\Models\VentaTemporada::class, 'email_usuario');
	}
}
