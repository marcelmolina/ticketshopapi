<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Cuponera
 * 
 * @property int $id
 * @property string $nombre
 * @property \Carbon\Carbon $fecha_inicio
 * @property \Carbon\Carbon $fecha_fin
 * @property int $status
 * 
 * @property \Illuminate\Database\Eloquent\Collection $cupons
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 *
 * @package App\Models
 */
class Cuponera extends Eloquent
{
	protected $table = 'cuponera';
	public $timestamps = false;

	protected $casts = [
		'status' => 'int'
	];

	protected $dates = [
		'fecha_inicio',
		'fecha_fin'
	];

	protected $fillable = [
		'nombre',
		'fecha_inicio',
		'fecha_fin',
		'status'
	];

	public function cupons()
	{
		return $this->hasMany(\App\Models\Cupon::class, 'id_cuponera');
	}

	public function eventos()
	{
		return $this->belongsToMany(\App\Models\Evento::class, 'evento_cuponera', 'id_cuponera', 'id_evento');
	}
}
