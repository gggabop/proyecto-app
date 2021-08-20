<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Customers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customers::where('register_status_db_customer',0)->get();
        return response(['Message'=>'Ok'
                        ,'Clientes'=>$customers],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    // TODO: realizar la nacionalidad, y agregar el prefijo de pais a los numeros de telefono
    // TODO 2: Expandir la expresion regular!!!

    {
        $validator = Validator::make($request->all(), [
            'name_customer' => array('required',
                            'regex:/(^([a-zA-Z ]+)(\d+)?$)/u'),
            'cedula_customer' => 'required|numeric|unique:customers',
            'address_work_customer' => array('required',
                                             'regex:/(^([a-zA-Z0-9 ]+)(\d+)?$)/u'),
            'address_home_customer' => array('required',
                                            'regex:/(^([a-zA-Z0-9 ]+)(\d+)?$)/u'),
            'extra_address_customer'=> array('regex:/(^([a-zA-Z0-9 ]+)(\d+)?$)/u'),
            'cellphone_customer'=> 'required|numeric',
            'extra_cellphone_customer'=> 'numeric',
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $ValidData=$validator->validated();
        $customers = new Customers($ValidData);
        $customers->save();
        $datosAuditoria = ['description_aud'=> 'creacion de cliente nombre:'.$request->name_customer,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'creacion cliente'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['Message'=>'Cliente Agregado',
                         'Data'=>$ValidData],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //TODO: agregar prestamos del cliente y los pagos de esos prestamos si es posible
        $cliente = Customers::where('id',$id)->where('register_status_db_customer',0)->get();
        if ($cliente->isEmpty()) {
            return response(['Message'=>'Customer 404']);
        }
        return response(['Cliente' => $cliente]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $cliente = Customers::find($id);
        if (empty($cliente)) {
            return response(['Message'=>'Customer 404']);
        }
        $validator = Validator::make($request->all(), [
            'name_customer' => array('required',
                            'regex:/(^([a-zA-Z ]+)(\d+)?$)/u'),
            'cedula_customer' => 'required|numeric',
            'address_work_customer' => array('required',
                                             'regex:/(^([a-zA-Z0-9 ]+)(\d+)?$)/u'),
            'address_home_customer' => array('required',
                                            'regex:/(^([a-zA-Z0-9 ]+)(\d+)?$)/u'),
            'extra_address_customer'=> array('regex:/(^([a-zA-Z0-9 ]+)(\d+)?$)/u'),
            'cellphone_customer'=> 'required|numeric',
            'extra_cellphone_customer'=> 'numeric',
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $ValidData=$validator->validated();
        $cliente->update($ValidData);
        $datosAuditoria = ['description_aud'=> 'actualizacion de cliente nombre:'.$request->name_customer,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'actualizacion cliente'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['cliente' => $cliente]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = Customers::find($id);
        if (empty($cliente)) {
            return response(['Message'=>'Customer 404']);
        }
        $cliente->register_status_db_customer = 1;
        $cliente->save();
        $datosAuditoria = ['description_aud'=> 'Eliminado de cliente nombre: '.$cliente->name_customer,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'borrado de cliente'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['Message' => 'Deleted Customer',
                         'Cliente' => $cliente->name_customer]);
    }
}
