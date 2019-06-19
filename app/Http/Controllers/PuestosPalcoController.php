<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use App\Models\Palco;
use App\Models\PuestosPalco;
use Validator;
/**
 * @group Administración de Puestos - Palco
 *
 * APIs para la gestion de la tabla asociativa puestos_palco
 */
class PuestosPalcoController extends BaseController
{
     /**
     * Listado de los Puestos por Palcos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $puesto_palco = PuestosPalco::with('palco')
                               ->with('puesto')
                               ->paginate(15);
        return $this->sendResponse($puesto_palco->toArray(), 'Puestos por palco devueltos con éxito');
    }

   /**
     * Agrega un nuevo elemento a la tabla puestos_palco 
     *@bodyParam id_palco int required Id del palco.
     *@bodyParam id_puesto int required Id del puesto.
     * @response {
     *  "id_palco": 1,
     *  "id_puesto": 1,      
     * }  
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_palco' => 'required|integer',
            'id_puesto' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $palco = Palco::find($request->input('id_palco'));
        if (is_null($palco)) {
            return $this->sendError('El palco indicado no existe');
        }

        $puesto = Puesto::find($request->input('id_puesto'));
        if (is_null($puesto)) {
            return $this->sendError('El puesto indicado no existe');
        }

        $puesto_palco_search = PuestosPalcoController::puesto_palco_search($request->input('id_palco'), $request->input('id_puesto'));

        if(count($puesto_palco_search) != 0){
           return $this->sendError('El palco ya posee el puesto asociada'); 
        }
        

        $puesto_palco = PuestosPalco::create($request->all());        
        return $this->sendResponse($puesto_palco->toArray(), 'El puesto por palco creado con éxito');
    }

    /**
     * Lista de los puestos por palco en especifico
     * [Se filtra por el ID del palco]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $puesto_palco = PuestosPalco::where('id_palco','=',$id)->get();
        if (count($puesto_palco) == 0) {
            return $this->sendError('Puestos por palco no encontrados');
        }
        return $this->sendResponse($puesto_palco->toArray(), 'Puestos por palco devueltos con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla puestos_palco 
     *
     * [Se filtra por el ID del palco]
     *@bodyParam id_puesto_old int required Id del puesto (El cual se quiere editar).
     *@bodyParam id_puesto_new int required Id del puesto (Id nuevo de la cuponera).
     *@response {
     *  "id_puesto_old": 1,
     *  "id_puesto_new": 2,      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [            
            'id_puesto_old' => 'required|integer',
            'id_puesto_new' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());      
        }
        $puesto_palco_search = PuestosPalcoController::puesto_palco_search($id, $input['id_puesto_old']);
        
        if(count($puesto_palco_search) != 0){

            $palco = Palco::find($id);
            if (is_null($palco)) {
                return $this->sendError('El palco indicado no existe');
            }

            $puesto = Puesto::find($input['id_puesto_new']);
            if (is_null($puesto)) {
                return $this->sendError('El puesto indicado no existe');
            }

            $puesto_palco_search2 = PuestosPalcoController::puesto_palco_search($id, $input['id_puesto_new']);
            
            if(count($puesto_palco_search2) != 0){
                return $this->sendError('El puesto por palco ya se encuentra asociado'); 
            }
            
        }else{
           return $this->sendError('El puesto por palco no se encuentra'); 
        }

        PuestosPalco::where('id_palco','=',$id)
                            ->where('id_puesto','=', $input['id_puesto_old'])
                            ->update(['id_puesto' => $input['id_puesto_new']]);  
        
        $puesto_palco = PuestosPalcoController::puesto_palco_search($id, $input['id_puesto_new']);
                            
        return $this->sendResponse($puesto_palco->toArray(), 'Puesto por palco actualizado con éxito');
    }

    /**
     * Elimina todos los elemento de la tabla puestos_palco
     *
     * [Se filtra por el ID del palco]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $puesto_palco = PuestosPalco::where('id_palco','=',$id)->get();
        if (count($puesto_palco) == 0) {
            return $this->sendError('Puestos por palco no encontradas');
        }
        PuestosPalco::where('id_palco','=',$id)->delete();
        return $this->sendResponse($puesto_palco->toArray(), 'Puestos por palco eliminados con éxito');
    }

    public function puesto_palco_search($id_palco, $id_puesto){

        $search = PuestosPalco::where('id_palco','=',$id_palco)
                                ->where('id_puesto','=', $id_puesto)->get();
        return $search;
    }
}
