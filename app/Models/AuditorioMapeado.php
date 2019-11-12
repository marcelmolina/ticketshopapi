<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 1 Oct 2019 9:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuditorioMapeado
 * 
 * @property int $id 
 * @property int $id_auditorio
 * @property string $area_mapeada
 * @property string $imagen
 * 
 * @property \Illuminate\Database\Eloquent\Collection $auditorios 
 * @property \Illuminate\Database\Eloquent\Collection $tribunas
 *
 * @package App\Models
 */
class AuditorioMapeado extends Eloquent
{
	protected $table = 'auditorio_mapeado';
	public $timestamps = false;

	protected $casts = [
		'id_auditorio' => 'int'
	];

	protected $fillable = [		
		'id_auditorio',
		'area_mapeada',
		'imagen'
	];	

	public function auditorio()
	{
		return $this->belongsTo(\App\Models\Auditorio::class, 'id_auditorio');
	}	

	public function eventos()
	{
		return $this->hasMany(\App\Models\Evento::class, 'id_auditorio_mapeado');
	}

	public function tribunas()
	{
		return $this->belongsToMany(\App\Models\Tribuna::class, 'auditorio_mapeado_tribuna', 'id_auditorio_mapeado', 'id_tribuna');
	}
}
