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
 * @property string $order
 * @property string $message
 * @property date $time
 * @property string $customer
 * @property int $amount
 * @property string $currency
 * @property string $status
 * @property string $authorization
 * @property string $token
 * @property string $brand
 * @property string $country
 * @property string $ip
 * @property string $client
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

	public function venta()
	{
		return $this->belongsTo(\App\Models\Vent::class, 'order', 'token_refventa');
	}
}

