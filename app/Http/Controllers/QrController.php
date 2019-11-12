<?php

namespace App\Http\Controllers;

use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Servicios QR
 *
 * Servicios para la gestión de los QR de las Boletas y Palcos de los eventos
 */
class QrController extends BaseController
{

    /**
     * Obtener QR de la Boleta o Palco
     *
     * [Se envía el ID del palco o de la boleta según se requiera]
	 *
     *@bodyParam id_palco_evento int ID del palco evento.
     *@bodyParam id_boleta_evento int ID del boleta evento.
     *
     *@response{
     *       "id_palco_evento" : 2,
     *       "id_boleta_evento" : null               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_qr(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'id_palco_evento' => 'nullable|integer', 
            'id_boleta_evento' => 'nullable|integer'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(is_null($request->input('id_palco_evento')) && is_null($request->input('id_boleta_evento'))){
        	return $this->sendError('Debe especificar el id de la boleta o del palco');
        }

        if(!is_null($request->input('id_palco_evento')) && !is_null($request->input('id_boleta_evento'))){
        	return $this->sendError('Debe especificar el id de la boleta o del palco, no ambos');
        }

        if( !is_null($request->input('id_boleta_evento')) ){
        	$id = $request->input('id_boleta_evento');
	        $boleta_evento = BoletaEvento::find($id);                   
	        if (!$boleta_evento) {
	            return $this->sendError('La boleta evento no se encuentra');
	        }
	        $token = $boleta_evento->token_qr;
	    }

	    if( !is_null($request->input('id_palco_evento')) ){
	    	$id = $request->input('id_palco_evento');
	        $palco_evento = PalcoEvento::find($id);                   
	        if (!$palco_evento) {
	            return $this->sendError('El palco evento no se encuentra');
	        }
	        $token = $palco_evento->token_qr;
	    }


        $QR = \QrCode::format('png')->size(500)->errorCorrection('H')->generate($token);
        if($QR == null){
            return $this->sendError('Error al obtener QR');
        }
        return response($QR)->header('Content-type','image/png');
    }


    /**
     * Obtener infomación de la Boleta o del Palco 
     *
     * [Se utiliza el TOKEN escaneado del QR]
     *
     *@bodyParam token string required Token.
     *@response{
     *       "token" : "9d7695445b92e5cadffb95db498987"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function info_token_qr(Request $request)
    {
        
    	$validator = Validator::make($request->all(), [
            'token' =>  'required|string'          
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
                
        $token = $request->input('token');
        if(!$token || is_null($token) ){
            return $this->sendError('Debe proporcionar un token');
        }
                
        $boleta_evento = BoletaEvento::with("evento")
                    ->with("puesto")
                    ->with("codigo_moneda")
                    ->where('token_qr', $token)
                    ->first();
        if (!$boleta_evento) {

        	$palco_evento = PalcoEvento::with("evento")
                    ->with("palco")
                    ->with("moneda")
                    ->where('token_qr', $token)
                    ->first();
             if(!$palco_evento){
             	
             	return $this->sendError('No se encuentra ningún palco ni boleta asociado al token.');
             
             }else{
             	
             	return $this->sendResponse($palco_evento->toArray(), 'La boleta del evento devuelta con éxito');
             }
            
        }else{

	        return $this->sendResponse($boleta_evento->toArray(), 'La boleta del evento devuelta con éxito');
        
        }
    	
    }
}
