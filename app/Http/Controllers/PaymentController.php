<?php

namespace App\Http\Controllers;
use App\Models\Pago;
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
        $pago->currency = $request->input('currency');
        $pago->status = $request->input('Status');

        $pago->authorization = $request->input('Authorization');
        $pago->token = $request->input('Token');
        $pago->brand = $request->input('Brand');
        $pago->country = $request->input('Country');
        $pago->ip = $request->input('Ip');
        $pago->client = $request->input('Client');

        $pago->save();

    }

    public function generarsha(Request $request){

        $validator = Validator::make($request->all(), [          
            'refventa' => 'required|integer',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $info_pago = new stdClass();
       
        $info_pago->usuarioId = 1963; // TicketShop
        $info_pago->claveSecretausuarioID = '2cff6cd4b00c7ace4b2615bb99db4511'; // TicketShop

        $info_pago->Moneda=$request->Moneda;
        $info_pago->refVenta= $request->input('refventa');
        $info_pago->URLRespuesta='http://18.209.56.187/paymentconfirm';
        $info_pago->URLConfirma = 'http://api.ticketshop.com.co/api/payment_confirm';

        $info_pago->firma_codificada = openssl_digest( $info_pago->usuarioId . $info_pago->refVenta . $info_pago->Moneda . $info_pago->claveSecretausuarioID, 'sha512' );

        return $this->sendResponse(get_object_vars($info_pago), 'Cliente devuelto con éxito');
    }

}
