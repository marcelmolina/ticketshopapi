<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cupon;
use App\Models\TipoCupon;
use App\Models\Moneda;
use App\Models\Cuponera;
use Validator;
use Illuminate\Support\Facades\Input;

/**
 * @group Administración de Cupon
 *
 * APIs para la gestion de cupon
 */
class CuponController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }


    /**
     * Lista de la tabla cupon.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cupon = Cupon::paginate(15);
        return $this->sendResponse($cupon->toArray(), 'Cupones devueltos con éxito');
    }


    /**
     * Listado detallado de los cupones.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_cupones()
    {
        
        $cupon = Cupon::with('genero')
                ->with('tipo_cupon')
                ->with('cuponera')
                ->with('moneda')
                ->paginate(15);
        $lista_cupon = compact('cupon');
        return $this->sendResponse($lista_cupon, 'Cupones devueltos con éxito');
    }

  
   /**
     * Agrega un nuevo elemento a la tabla cupon
     *@bodyParam codigo string Codigo del cupon.
     *@bodyParam status int required Status del cupon.
     *@bodyParam monto int Monto del cupon.
     *@bodyParam porcentaje_descuento int Porcentaje de descuento del cupon.
     *@bodyParam id_tipo_cupon int required Id del tipo de cupon.
     *@bodyParam id_cuponera int required Id de la cuponera.
     *@bodyParam cantidad_compra int  Cantidad de compra.
     *@bodyParam cantidad_paga int Cantidad de paga.
     *@bodyParam codigo_moneda string required Codigo moneda.    
     *@response{
     *       "codigo" : null,
     *       "status" : 1,
     *       "monto": null,
     *       "porcentaje_descuento": null,
     *       "id_tipo_cupon" : 1,
     *       "id_cuponera": 1,
     *       "cantidad_compra": null,
     *       "cantidad_paga": null,
     *       "codigo_moneda": "USD"
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'id_tipo_cupon' => 'required',
            'id_cuponera' => 'required',
            'codigo_moneda' => 'required',      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $tipo_cupon = TipoCupon::find($request->input('id_tipo_cupon'));
        if (is_null($tipo_cupon)) {
            return $this->sendError('El tipo de cupon indicado no existe');
        }

        $cuponera = Cuponera::find($request->input('id_cuponera'));
        if (is_null($cuponera)) {
            return $this->sendError('La cuponera indicada no existe');
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }

        if(!is_null($request->input('monto'))){
            $validator = Validator::make($request->all(), [
                'monto' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }else{
            Input::merge(['monto' => 0]);
        }

        if(!is_null($request->input('porcentaje_descuento'))){
            $validator = Validator::make($request->all(), [
                'porcentaje_descuento' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }else{
            Input::merge(['porcentaje_descuento' => 0]);
        }

        $cupon = Cupon::create($request->all());        
        return $this->sendResponse($cupon->toArray(), 'Cupon creado con éxito');

    }

    /**
     * Lista un cupon en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cupon = Cupon::find($id);

        if (is_null($cupon)) {
            return $this->sendError('Cupon no encontrado');
        }
        return $this->sendResponse($cupon->toArray(), 'Cupon devuelto con éxito');
    }


     /**
     * Actualiza un elemeto de la tabla cupon 
     *
     * [Se filtra por el ID]
     *@bodyParam codigo string Codigo del cupon.
     *@bodyParam status int required Status del cupon.
     *@bodyParam monto int Monto del cupon.
     *@bodyParam porcentaje_descuento int Porcentaje de descuento del cupon.
     *@bodyParam id_tipo_cupon int required Id del tipo de cupon.
     *@bodyParam id_cuponera int required Id de la cuponera.
     *@bodyParam cantidad_compra int  Cantidad de compra.
     *@bodyParam cantidad_paga int Cantidad de paga.
     *@bodyParam codigo_moneda string required Codigo moneda..    
     *@response{
     *       "codigo" : "75647563jfghhg",
     *       "status" : 1,
     *       "monto": 200,
     *       "porcentaje_descuento": 5,
     *       "id_tipo_cupon" : 1,
     *       "id_cuponera": 1,
     *       "cantidad_compra": null,
     *       "cantidad_paga": null,
     *       "codigo_moneda": "COP"
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required',
            'id_tipo_cupon' => 'required',
            'id_cuponera' => 'required',
            'codigo_moneda' => 'required',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $cupon_search = Cupon::find($id);
        if (is_null($cupon_search)) {
            return $this->sendError('Cupon no encontrado');
        } 

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }

        $tipo_cupon = TipoCupon::find($input['id_tipo_cupon']);
        if (is_null($tipo_cupon)) {
            return $this->sendError('El tipo de cupon indicado no existe');
        }

        $cuponera = Cuponera::find($input['id_cuponera']);
        if (is_null($cuponera)) {
            return $this->sendError('La cuponera indicada no existe');
        }

        if(!is_null($input['monto'])){
            $validator = Validator::make($request->all(), [
                'monto' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $cupon_search->monto = $input['monto'];
        }else{
            $cupon_search->monto = 0;
        }

        if(!is_null($input['porcentaje_descuento'])){
            $validator = Validator::make($request->all(), [
                'porcentaje_descuento' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $cupon_search->monto = $input['porcentaje_descuento'];
        }else{
            $cupon_search->porcentaje_descuento = 0;
        }

        $cupon_search->codigo = $input['codigo'];
        $cupon_search->status = $input['status'];        
        $cupon_search->id_tipo_cupon = $input['id_tipo_cupon'];        
        $cupon_search->id_cuponera = $input['id_cuponera'];
        $cupon_search->cantidad_compra = $input['cantidad_compra'];        
        $cupon_search->cantidad_paga = $input['cantidad_paga'];
        $cupon_search->codigo_moneda = $input['codigo_moneda'];
        $cupon_search->save();
        return $this->sendResponse($cupon_search->toArray(), 'Cupon actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla cupon
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       try { 

            $cupon = Cupon::find($id);
            if (is_null($cupon)) {
                return $this->sendError('Cupon no encontrado');
            }
            $cupon->delete();
            return $this->sendResponse($cupon->toArray(), 'Cupon eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Cupon no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
