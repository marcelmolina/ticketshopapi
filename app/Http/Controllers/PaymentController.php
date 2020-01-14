<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Vent;
use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use App\Models\DetalleVent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

class PaymentController extends BaseController
{
    public function payment_confirm(Request $request){


        $date = explode('T', $request->input('Time'));
        $time = explode(' ', $date[1]); 
        $datetime = $date[0].' '.$time[0];
        
        $pago = new Pago();

        $pago->order = $request->input('Order');
        $pago->code = $request->input('Code');
        $pago->message = $request->input('Message');
        $pago->time = $datetime;
        $pago->customer = $request->input('Customer');
        $pago->amount = $request->input('Amount');

        if($request->input('Currency') == 604){
            $pago->currency = 'SOL';
        }
        else if($request->input('Currency') == 840){
            $pago->currency = 'USD';
        }
        else{
            $pago->currency = 'COP';
        }

        $pago->status = $request->input('Status');

        $pago->authorization = $request->input('Authorization');
        $pago->token = $request->input('Token');
        $pago->brand = $request->input('Brand');
        $pago->country = $request->input('Country');
        $pago->ip = $request->input('Ip');
        $pago->client = $request->input('Client');

        $pago->save();

        $venta = Vent::where('token_refventa',$pago->order)->first();
        $id_venta = $venta->id;
        $status = 1;
        if($pago->status = 'SUCCESS'){            

            Vent::find($id_venta)->update(['active' => 1]);
            $detalle_v = DetalleVent::where('id_venta',$id_venta)->update(['status' => 1]);
            $status = 7;
        }


        $boletas = DetalleVent::where('id_venta',$id_venta)->whereNull('id_palco_evento')->select('id_boleta_evento')->get();

                
        foreach ($boletas as $boletas_id) {
		BoletaEvento::where('id', $boletas_id->id_boleta_evento)->update(['status' => $status]);       
	 }


        $palcos = DetalleVent::where('id_venta',$id_venta)->whereNull('id_boleta_evento')->select('id_palco_evento')->get();
        
        foreach ($palcos as $palcos_id) {
		PalcoEvento::where('id', $value_palco->id_palco_evento)->update(['status' => $status]);
        }



    }

}

