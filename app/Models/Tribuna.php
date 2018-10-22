<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Tribuna
 * 
 * @property int $id
 * @property string $nombre
 * @property int $id_auditorio
 * 
 * @property \App\Models\Auditorio $auditorio
 * @property \Illuminate\Database\Eloquent\Collection $detalle_descuentos
 * @property \Illuminate\Database\Eloquent\Collection $localidads
 *
 * @package App\Models
 */
class Tribuna extends Eloquent
{
	protected $table = 'tribuna';
	public $timestamps = false;

	protected $casts = [
		'id_auditorio' => 'int'
	];

	protected $fillable = [
		'nombre',
		'id_auditorio'
	];

	public function auditorio()
	{
		return $this->belongsTo(\App\Models\Auditorio::class, 'id_auditorio');
	}

	public function detalle_descuentos()
	{
		return $this->hasMany(\App\Models\DetalleDescuento::class, 'id_tribuna');
	}

	public function localidads()
	{
		return $this->hasMany(\App\Models\Localidad::class, 'id_tribuna');
	}
}
