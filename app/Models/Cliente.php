<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Cliente
 * 
 * @property int $id
 * @property string $Identificacion
 * @property bool $tipo_identificacion
 * @property string $nombrerazon
 * @property string $direccion
 * @property string $ciudad
 * @property string $departamento
 * @property bool $tipo_cliente
 * @property string $email
 * @property string $telefono
 * 
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 *
 * @package App\Models
 */
class Cliente extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'tipo_identificacion' => 'bool',
		'tipo_cliente' => 'bool'
	];

	protected $fillable = [
		'Identificacion',
		'tipo_identificacion',
		'nombrerazon',
		'direccion',
		'ciudad',
		'departamento',
		'tipo_cliente',
		'email',
		'telefono'
	];

	public function eventos()
	{
		return $this->hasMany(\App\Models\Evento::class, 'id_cliente');
	}
}
