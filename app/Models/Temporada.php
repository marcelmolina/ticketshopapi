<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Temporada
 * 
 * @property int $id
 * @property string $nombre
 * @property bool $status
 * 
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @property \Illuminate\Database\Eloquent\Collection $venta_temporadas
 *
 * @package App\Models
 */
class Temporada extends Eloquent
{
	protected $table = 'temporada';
	public $timestamps = false;

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'nombre',
		'status'
	];

	public function eventos()
	{
		return $this->hasMany(\App\Models\Evento::class, 'id_temporada');
	}

	public function venta_temporadas()
	{
		return $this->hasMany(\App\Models\VentaTemporada::class, 'id_temporada');
	}
}
