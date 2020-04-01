<?php 
namespace App\Http\Services;

use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use App\Models\PuestosPalcoEvento;

class PaymentService
{

	public function consulta_transaccion($acquirerId , $idCommerce, $purchaseOperationNumber)
    {
    	
    	$claveSecreta = env('PRIVATE_KEY_PAYMENT');
    	$url = 'https://integracion.alignetsac.com/VPOS2/rest/operationAcquirer/consulte';    	

    	$purchaseVerification = openssl_digest($acquirerId.$idCommerce.$purchaseOperationNumber.$claveSecreta, 'sha512');

    	$dataRest = '{"idAcquirer":"'.$acquirerId.'","idCommerce":"'.$idCommerce.'","operationNumber":"'.$purchaseOperationNumber.'","purchaseVerification":"'.$purchaseVerification.'"}';

    	
    	$curl = curl_init();
		curl_setopt($curl, CURLOPT_POSTFIELDS, $dataRest);
		curl_setopt($curl, CURLOPT_URL, $url);
		//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);
		$code=curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
    	
		return  ;

    }

    public function autorizacion($acquirerId , $idCommerce, $purchaseOperationNumber)
    {

    	$codigomoneda = 'COU';
    	$claveSecreta = env('PRIVATE_KEY_PAYMENT');
    	$url = 'https://integracion.alignetsac.com/VPOS2/rest/operationAcquirer/authorize';

    }
}