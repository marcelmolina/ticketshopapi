<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Input;

/**
 * @group Administración de Cliente
 *
 * APIs para la gestion de cliente
 */
class ClienteController extends BaseController
{
    /**
     * Lista de la tabla cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $cliente = Cliente::paginate(15);

        return $this->sendResponse($cliente->toArray(), 'Clientes devueltos con éxito');
    }


    /**
     * Buscar Clientes por nombre.
     *@bodyParam nombre string Nombre_razon del cliente.
     *@response{
     *    "nombre" : "CLiente 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarClientes(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $cliente = Cliente::where('clientes.nombrerazon','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($cliente->toArray(), 'Todos los Clientes filtrados');
       }else{
            
            $cliente = Cliente::get();
            return $this->sendResponse($cliente->toArray(), 'Todos los Clientes devueltos'); 
       }

        
    }


    /**
     * Agrega un nuevo elemento a la tabla cliente
     *@bodyParam Identificacion string required Identificacion del cliente.
     *@bodyParam tipo_identificacion boolean required Tipo de identificacion del cliente.
     *@bodyParam nombrerazon string required Nombre razon del cliente.
     *@bodyParam direccion string required Direccion del cliente.
     *@bodyParam ciudad string Ciudad del cliente.
     *@bodyParam departamento string Departamento del cliente.
     *@bodyParam tipo_cliente boolean required Tipo de cliente.
     *@bodyParam email string required Email del cliente.
     *@bodyParam telefono string required Telefono del cliente.
     *@response{
     *       "Identificacion" : "Cliente Platinium",
     *       "tipo_identificacion" : 1,
     *       "nombrerazon": "Company",
     *       "direccion": "Street 22",
     *       "ciudad" : null,
     *       "departamento": null,
     *       "tipo_cliente": 1,
     *       "email": "cliente@example.com",
     *       "telefono": "5788722330092"
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
       $validator = Validator::make($request->all(), [
            'Identificacion'=> 'required' ,
            'tipo_identificacion' => 'required|boolean',
            'nombrerazon' => 'required',
            'direccion' => 'required',
            'tipo_cliente' => 'required|boolean',
            'email' => 'required|email',
            'telefono' => 'required',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $cliente = Cliente::create($request->all());        
        return $this->sendResponse($cliente->toArray(), 'Cliente creado con éxito');
    }

    /**
     * Lista un cliente en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $cliente = Cliente::find($id);
        if (is_null($cliente)) {
            return $this->sendError('Cliente no encontrado');
        }
        return $this->sendResponse($cliente->toArray(), 'Cliente devuelto con éxito');
    }

     /**
     * Actualiza un elemeto de la tabla cliente 
     *
     * [Se filtra por el ID]
     *@bodyParam Identificacion string required Identificacion del cliente.
     *@bodyParam tipo_identificacion boolean required Tipo de identificacion del cliente.
     *@bodyParam nombrerazon string required Nombre razon del cliente.
     *@bodyParam direccion string required Direccion del cliente.
     *@bodyParam ciudad string Ciudad del cliente.
     *@bodyParam departamento string Departamento del cliente.
     *@bodyParam tipo_cliente boolean required Tipo de cliente.
     *@bodyParam email string required Email del cliente.
     *@bodyParam telefono string required Telefono del cliente.
     *@response{
     *       "Identificacion" : "Cliente Platinium",
     *       "tipo_identificacion" : 1,
     *       "nombrerazon": "Company B.C.",
     *       "direccion": "Street 25",
     *       "ciudad" : null,
     *       "departamento": "Hotel Royal A-0065",
     *       "tipo_cliente": 1,
     *       "email": "cliente@example.com",
     *       "telefono": "5788722330092"
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'Identificacion'=> 'required' ,
            'tipo_identificacion' => 'required|boolean',
            'nombrerazon' => 'required',
            'direccion' => 'required',
            'tipo_cliente' => 'required|boolean',
            'email' => 'required|email',
            'telefono' => 'required',          
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $cliente = Cliente::find($id);
        if (is_null($cliente)) {
            return $this->sendError('Cliente no encontrado');
        }
        $cliente->Identificacion = $input['Identificacion'];
        $cliente->tipo_identificacion = $input['tipo_identificacion'];
        $cliente->nombrerazon = $input['nombrerazon'];
        $cliente->direccion = $input['direccion']; 
        $cliente->ciudad = $input['ciudad'];
        $cliente->departamento = $input['departamento'];   
        $cliente->tipo_cliente = $input['tipo_cliente'];
        $cliente->email = $input['email']; 
        $cliente->telefono = $input['telefono'];        
        $cliente->save();

        return $this->sendResponse($cliente->toArray(), 'Cliente actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla cliente
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {

            $cliente =Cliente::find($id);
            if (is_null($cliente)) {
                return $this->sendError('Cliente no encontrado');
            }
            $cliente->delete();
            return $this->sendResponse($cliente->toArray(), 'Cliente eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Cliente no se puedo eliminar, el registro esta siendo usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }


        
    }
}
