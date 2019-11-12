<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Pago
 * 
 * @property int $id
 * @property string $description
 * 
 * @property \Illuminate\Database\Eloquent\Collection $artists
 *
 * @package App\Models
 */
class Pago extends Eloquent
{
	protected $table = 'pagos';
	public $timestamps = false;

	protected $fillable = [
		'order',
		'code',
		'message',
		'time',
		'customer',
		'amount',
		'currency',
		'status',
		'authorization',
		'token',
		'brand',
		'country',
		'ip',
		'client'
	];

	protected $hidden = [
		'authorization',
		'token',
		'ip',
		'client'
	];

	
}
