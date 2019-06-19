<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PuestosPalco
 * 
 * @property int $id_palco
 * @property int $id_puesto
 * 
 * @property \App\Models\Palco $palco
 * @property \App\Models\Puesto $puesto
 * @property \Illuminate\Database\Eloquent\Collection $puestos_palco_eventos
 *
 * @package App\Models
 */
class PuestosPalco extends Eloquent
{
	protected $table = 'puestos_palco';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_palco' => 'int',
		'id_puesto' => 'int'
	];

	protected $fillable = [
		'id_palco',
		'id_puesto'
	];

	public function palco()
	{
		return $this->belongsTo(\App\Models\Palco::class, 'id_palco');
	}

	public function puesto()
	{
		return $this->belongsTo(\App\Models\Puesto::class, 'id_puesto');
	}

	public function puestos_palco_eventos()
	{
		return $this->hasMany(\App\Models\PuestosPalcoEvento::class, 'id_puesto', 'id_puesto');
	}
}
