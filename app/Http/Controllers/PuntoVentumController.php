<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoVentum;
use App\Models\Ciudad;
use Validator;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Puntos de Venta
 *
 * APIs para la gestion de la tabla punto_venta
 */
class PuntoVentumController extends BaseController
{
    /**
     * Lista de la tabla punto_venta paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $punto_venta = PuntoVentum::with("ciudades")->paginate(15);
        return $this->sendResponse($punto_venta->toArray(), 'Puntos de ventas devueltos con éxito');
    }


    /**
     * Lista de los puntos de venta.
     *
     * @return \Illuminate\Http\Response
     */
    public function puntoventum_all()
    {
        $punto_venta = PuntoVentum::with("ciudades")->get();
        return $this->sendResponse($punto_venta->toArray(), 'Puntos de ventas devueltos con éxito');
    }



    /**
     * Buscar Punto de venta por nombre razon.
     *@bodyParam nombre string Nombre_razon del punto de venta.
     *@response{
     *    "nombre" : "Nombre razon",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarPuntoVentum(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $punto_venta = PuntoVentum::with("ciudades")
                ->where('punto_venta.nombre_razon','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($punto_venta->toArray(), 'Todos los Punto de venta filtrados');
       }else{
            
            $punto_venta = PuntoVentum::with("ciudades")->get();
            return $this->sendResponse($punto_venta->toArray(), 'Todos los Punto de venta devueltos'); 
       }

        
    }


    /**
     * Agrega un nuevo elemento a la tabla punto_venta
     *
     *@bodyParam nombre_razon string required Nombre razon del punto de venta.
     *@bodyParam identificacion string required required Identificacion del punto de venta.
     *@bodyParam tipo_identificacion int required Tipo de identificacion punto de venta.
     *@bodyParam direccion string Direccion del punto de venta.
     *@bodyParam telefono string Telefono del punto de venta.
     *@bodyParam responsable string required Responsable del punto de venta.
     *@bodyParam zona string required Zona.
     *@bodyParam email string required Email.
     *@bodyParam id_ciudad int required ID de la ciudad.
     *
     * @response {      
     *  "nombre_razon": "BBV", 
     *  "identificacion": "BBV",
     *  "tipo_identificacion": 1,
     *  "direccion" : "Address One",
     *  "telefono" : "311998333",
     *  "responsable": "Responsable",
     *  "zona" : "Zona 1",
     *  "email" : "responsable@gmail.com",
     *  "id_ciudad" : 1      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_razon' => 'required',  
            'identificacion' => 'required',
            'tipo_identificacion' => 'required|integer',
            'responsable' => 'required',
            'zona' => 'required',
            'email' => 'required|email',
            'id_ciudad' => 'integer'  
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $ciudad = Ciudad::find($request->input('id_ciudad'));
        if (is_null($ciudad)) {
            return $this->sendError('La Ciudad indicada no existe');
        }      

        $punto_venta = PuntoVentum::create($request->all());        
        return $this->sendResponse($punto_venta->toArray(), 'Punto de venta creado con éxito');
    }

    /**
     * Lista de un punto de venta en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         
        $punto_venta = PuntoVentum::with("ciudades")->find($id);
        if (is_null($punto_venta)) {
            return $this->sendError('Punto de venta no encontrado');
        }
        return $this->sendResponse($punto_venta->toArray(), 'Punto de venta devuelto con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla punto_venta 
     * [Se filtra por el ID]
     *
     *@bodyParam nombre_razon string required Nombre razon del punto de venta.
     *@bodyParam identificacion string required required Identificacion del punto de venta.
     *@bodyParam tipo_identificacion int required Tipo de identificacion punto de venta.
     *@bodyParam direccion string Direccion del punto de venta.
     *@bodyParam telefono string Telefono del punto de venta.
     *@bodyParam responsable string required Responsable del punto de venta.
     *@bodyParam zona string required Zona.
     *@bodyParam email string required Email.
     *@bodyParam id_ciudad int required ID de la ciudad.
     *
     *
     * @response {
     *  "nombre_razon": "BBV", 
     *  "identificacion": "BBV",
     *  "tipo_identificacion": 0,
     *  "direccion" : "Address Two"
     *  "telefono" : "311998333",
     *  "responsable": "Responsable",
     *  "zona" : "Zona 1",
     *  "email" : "responsable@gmail.com",
     *  "id_ciudad" : 1   
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'nombre_razon' => 'required',  
            'identificacion' => 'required',
            'tipo_identificacion' => 'required|integer',
            'responsable' => 'required',
            'zona' => 'required',
            'email' => 'required|email',
            'id_ciudad' => 'integer'            
        ]);

        $punto_venta_search = PuntoVentum::find($id);        
        if (is_null($punto_venta_search)) {
            return $this->sendError('Punto de Venta no encontrado');
        } 

        $ciudad = Ciudad::find($input['id_ciudad']);
        if (is_null($ciudad)) {
            return $this->sendError('La Ciudad indicada no existe');
        }

        $punto_venta_search->nombre_razon = $input['nombre_razon'];
        $punto_venta_search->identificacion = $input['identificacion'];
        $punto_venta_search->tipo_identificacion = $input['tipo_identificacion'];         
        $punto_venta_search->direccion = $input['direccion'];
        $punto_venta_search->telefono = $input['telefono'];
        $punto_venta_search->responsable = $input['responsable'];
        $punto_venta_search->zona = $input['zona'];
        $punto_venta_search->email = $input['email'];
        $punto_venta_search->id_ciudad = $input['id_ciudad'];
        $punto_venta_search->save();

        return $this->sendResponse($punto_venta_search->toArray(), 'Punto de Venta actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla punto_venta
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $punto_venta = PuntoVentum::find($id);
            if (is_null($punto_venta)) {
                return $this->sendError('Punto de Venta no encontrado');
            }
            $punto_venta->delete();
            return $this->sendResponse($punto_venta->toArray(), 'Punto de Venta eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Punto de Venta no se puedo eliminar, el registro es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
