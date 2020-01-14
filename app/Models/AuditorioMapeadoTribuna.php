<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 1 Oct 2019 9:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuditorioMapeadoTribuna
 *  
 * @property int $id_auditorio_mapeado
 * @property int $id_tribuna
 * 
 * @property \Illuminate\Database\Eloquent\Collection $auditorio_mapeado 
 * @property \Illuminate\Database\Eloquent\Collection $tribuna
 *
 * @package App\Models
 */
class AuditorioMapeadoTribuna extends Eloquent
{
	protected $table = 'auditorio_mapeado_tribuna';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_auditorio_mapeado' => 'int',
		'id_tribuna' => 'int'
	];

	protected $fillable = [		
		'id_auditorio_mapeado',
		'id_tribuna'		
	];	

	public function auditorio_mapeado()
	{
		return $this->belongsTo(\App\Models\AuditorioMapeado::class, 'id_auditorio_mapeado');
	}	

	public function tribuna()
	{
		return $this->belongsTo(\App\Models\Tribuna::class, 'id_tribuna');
	}
}

