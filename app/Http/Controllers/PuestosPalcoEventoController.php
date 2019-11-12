<?php

namespace App\Http\Controllers;

use App\Models\PuestosPalcoEvento;
use App\Models\PalcoEvento;
use App\Models\BoletaEvento;
use App\Models\Puesto;
use App\Models\Palco;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Puestos-Palco-Evento
 *
 * APIs para la gestion de la tabla asociativa puestos_palco_evento
 */
class PuestosPalcoEventoController extends BaseController
{
    

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'edit', 'update', 'destroy']]);        
    }


    /**
     * Listado de los Puestos palco por evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $puesto_palco_evento = PuestosPalcoEvento::with('palco_evento')
                                ->with("palco")
                                ->with("puesto")  
                                ->paginate(15);
        return $this->sendResponse($puesto_palco_evento->toArray(), 'Puestos palco por evento devueltos con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla puestos_palco_evento
     * Se valida que el puesto no esté asignado a otro boleto para ese evento
     *@bodyParam id_evento int required Id del evento presente en la tabla PalcoEvento.
     *@bodyParam id_palco int required Id del palco presente en la tabla PalcoEvento.
     *@bodyParam id_puesto int required Id del puesto.
     * @response {
     *  "id_evento": 1,
     *  "id_palco": 1,
     *  "id_puesto": 1,     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento' => 'required|integer',
            'id_palco' => 'required|integer',
            'id_puesto' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $palcoevento = PalcoEvento::where('id_evento', $request->input('id_evento'))
                            ->where('id_palco', $request->input('id_palco'))
                            ->first();
        if (!$palcoevento) {
            return $this->sendError('El palco evento indicado no existe');
        }
        
        $id_palco_evento = $palcoevento->id;

        $puesto = Puesto::find($request->input('id_puesto'));
        if (is_null($puesto)) {
            return $this->sendError('El puesto indicado no existe');
        }       

        $puesto_palco__evento_search = $this->validate_palco_puesto($id_palco_evento, $request->input('id_palco'), $request->input('id_puesto'), $request->input('id_evento'));


        if(!$puesto_palco__evento_search){           
           return $this->sendError('No se puede agregar el registro. El puesto ya se encuentra asignado a una boleta del evento');         
        }else{   

            $puesto_palco_evento = new PuestosPalcoEvento();
            $puesto_palco_evento->id_palco_evento = $id_palco_evento;
            $puesto_palco_evento->id_palco = $request->input('id_palco');
            $puesto_palco_evento->id_puesto = $request->input('id_puesto');
            $puesto_palco_evento->save();

            return $this->sendResponse($puesto_palco_evento->toArray(), 'El puesto palco por evento creado con éxito');
        }

    }

    /**
     * Lista de puestos palco por evento en especifico 
     *
     * [Se filtra por el ID del palco_evento]
     *
     * @param  \App\Models\PuestosPalcoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $puesto_palco_evento = PuestosPalcoEvento::with("palco_evento")
                    ->with("palco")
                    ->with("puesto")                    
                    ->where('id_palco_evento','=',$id)
                    ->get();
        if (count($puesto_palco_evento) == 0) {
            return $this->sendError('Los puestos palcos del evento no se encuentra');
        }
        return $this->sendResponse($puesto_palco_evento->toArray(), 'Los puestos palcos del evento devueltos con éxito');
    }


    /**
     * Actualiza un elemento a la tabla puestos_palco_evento.
     *
     * [Se filtra por el ID del palco_evento]
     *
     * Se valida que el puesto no esté asignado a otro boleto para ese evento
     *
     *@bodyParam id_palco int required Id del palco (para ser actualizado).
     *@bodyParam id_puesto_old int required Id del puesto (para ser actualizado).
     *@bodyParam id_puesto_new int required Id del puesto (nuevo puesto a registrar).
     * @response {
     *  "id_palco": 1, 
     *  "id_puesto_old": 1,
     *  "id_puesto_new": 1,     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PuestosPalcoEvento  $puestosPalcoEvento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_palco' => 'required|integer',
            'id_puesto_old' => 'required|integer',
            'id_puesto_new' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        
        $puesto = Puesto::find($request->input('id_puesto_new'));
        if (is_null($puesto)) {
            return $this->sendError('El puesto indicado no existe');
        }

        $palcoevento = PalcoEvento::find($id);                            
        if (!$palcoevento) {
            return $this->sendError('El palco evento indicado no existe');
        }


        $puesto_palco_evento_search = $this->validate_palco_puesto($id, $request->input('id_palco'), $request->input('id_puesto_new'), $palcoevento->id_evento);


        if(!$puesto_palco_evento_search){           
           return $this->sendError('No se puede actualizar el registro. El puesto ya se encuentra asignado a una boleta para ese evento');         
        }else{ 

            $puesto_palco_evento = PuestosPalcoEvento::where('id_palco_evento', $id)
                            ->where('id_palco', $request->input('id_palco'))
                            ->where('id_puesto', $request->input('id_puesto_old'))
                            ->first(); 
            
            if($puesto_palco_evento){
                
                $puesto_palco_evento->update(['id_puesto' => $request->input('id_puesto_new')]);

                $puesto_palco_evento_con = PuestosPalcoEvento::with('palco')->with('puesto')->where('id_palco_evento', $id)
                            ->where('id_palco', $request->input('id_palco'))
                            ->where('id_puesto', $request->input('id_puesto_new'))
                            ->first();

                return $this->sendResponse($puesto_palco_evento_con->toArray(), 'El puesto palco por evento actualizado con éxito');

            }else{
                return $this->sendError('El puesto palco por evento indicado no existe');
            }
            
            
           
        }
    }



    /**
     * Elimina un elemento de la tabla puestos_palco_evento
     *
     * [Se filtra por el ID del palco_evento. Se eliminarán todos los registros que estén asignados a éste ID]
	 * 
     * @param  \App\Models\PuestosPalcoEvento  $puestosPalcoEvento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        try { 
            
            $puesto_palco_evento_con = PuestosPalcoEvento::where('id_palco_evento', $id)
                            			->get();  

                      
            if (!isset($puesto_palco_evento_con) || is_null($puesto_palco_evento_con) || sizeof($puesto_palco_evento_con)<1) {
                return $this->sendError('Puestos palcos por evento no encontrados');
            }
            
            $puesto_palco_evento = \DB::table('puestos_palco_evento')
                            ->where('id_palco_evento', $id)                            
                            ->delete();
            if($puesto_palco_evento){
                return $this->sendResponse($puesto_palco_evento_con->toArray(), 'Puestos palcos por evento eliminados con éxito');
            }

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Los Puestos palcos por evento no se pueden eliminar, son usados en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }


    public function validate_palco_puesto($id_palco_evento, $id_palco, $id_puesto, $id_evento){

        $puestos_palco_evento = PuestosPalcoEvento::where('id_palco_evento', $id_palco_evento)
                            ->where('id_palco', $id_palco)
                            ->where('id_puesto', $id_puesto)
                            ->first();

        if(!$puestos_palco_evento){            
                
            $boleta_evento = BoletaEvento::where('id_evento', $id_evento)
                            ->where('id_puesto', $id_puesto)
                            ->first();

            if (!$boleta_evento) {
                
                return true;
            
            }else{

                return false;
            }
            
        }else{
            return $this->sendError('El puesto ya se encuentra asignado al palco');            
        }

    }
}
