<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class Cliente
 * 
 * @property int $id
 * @property string $descripcion
 *
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @package App\Models
 */
class Condicion extends Eloquent
{
    protected $table = 'condiciones';
    public $timestamps = false;

    protected $fillable = [
		'descripcion'		
	];

	public function eventos()
	{
		return $this->belongsToMany(\App\Models\Evento::class, 'condiciones_evento', 'id_condiciones', 'id_evento');
	}
}
