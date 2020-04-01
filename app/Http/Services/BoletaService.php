<?php 
namespace App\Http\Services;

use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use App\Models\PuestosPalcoEvento;

class BoletaService
{
    
    /*
    $tipo  1 => Boleta_Evento
           2 => Palco_Evento
    */
    public function checkBoletaEvent($tipo, $id_evento, $id_puesto)
    {

        if($tipo == 1){

            $palco_evento = PalcoEvento::where('id_evento', $id_evento)->get();
            foreach ($palco_evento as $key) {                
                $puesto_palco_evento = PuestosPalcoEvento::where('id_palco_evento', $key->id)->where('id_puesto', $id_puesto)->first();
                if($puesto_palco_evento){
                    return true;
                }
            }

        }else{
            
            if($tipo == 2){

                $boleta_evento = BoletaEvento::where('id_evento', $id_evento)->where('id_puesto', $id_puesto)->first();
                if($boleta_evento){
                    return true;
                }

            }
           
        }
        return false;
        
    }
}