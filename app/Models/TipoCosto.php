<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class TipoCosto
 * 
 * @property int $id
 * @property string $descripcion
 * 
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 *
 * @package App\Models
 */
class TipoCosto extends Eloquent
{
    protected $table = 'tipo_costo';
    public $timestamps = false;

    protected $fillable = [
		'descripcion'		
	];

	public function eventos()
	{
		return $this->belongsToMany(\App\Models\Evento::class, 'costo_evento', 'id_tipo_costo', 'id_evento');
	}

}
