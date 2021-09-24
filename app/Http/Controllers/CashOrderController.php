<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\CashOrder;
use App\Models\Customers;
use App\Models\Loans;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
class CashOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cashOrders = CashOrder::where('register_status_db_cashOrder',0)->get();
        if ($cashOrders->isEmpty()) {
            return response(['Message'=>'No hay pedidos'],404);
        }
        return response(['message'=> 'Ok','pedidos'=>$cashOrders],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fk_customer_id' => 'required|numeric',
            'amount_cash_order' => array('required',
                                             'regex:/(^([0-9,. ]+)(\d+)?$)/u'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $customer = Customers::where('id',$request->fk_customer_id)->where('register_status_db_customer',0)->first();
        if (empty($customer)) {
            return response(['Message'=>'Cliente No Exite'],404);
        }
        $ValidData=$validator->validated();
        $cashOrder = new CashOrder($ValidData);
        $cashOrder->save();
        $datosAuditoria = ['description_aud'=> 'creacion de pedido prestamo para: '.$customer->name_customer,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'creacion pedido'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['message'=>'Ok',
                         'amount'=>$ValidData['amount_cash_order'],
                         'customer'=>$customer->name_customer]
                         ,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CashOrder  $cashOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cashOrder=CashOrder::find($id);
        if (empty($cashOrder)) {
            return response(['Message'=>'Pedido no existe']);
        }
        $customer=Customers::where('id',$cashOrder->fk_customer_id)->where('register_status_db_customer',0)->first();
        if (empty($customer)) {
            return response(['Message'=>'Cliente no existe']);
        }
        return response(['message'=>'Ok',
                         'pedido'=>$cashOrder,
                         'cliente'=>$customer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CashOrder  $cashOrder
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $cashOrder=CashOrder::find($id);
        if (empty($cashOrder)) {
            return response(['Message'=>'Pedido no existe']);
        }
        $validator = Validator::make($request->all(), [
            'fk_customer_id' => 'required|numeric',
            'amount_cash_order' => array('required',
                                             'regex:/(^([0-9,. ]+)(\d+)?$)/u'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $customer = Customers::where('id',$cashOrder->fk_customer_id)->where('register_status_db_customer',0)->first();
        if (empty($customer)) {
            return response(['Message'=>'Cliente No Exite'],404);
        }
        $ValidData=$validator->validated();
        $cashOrder->update($ValidData);
        $datosAuditoria = ['description_aud'=> 'actualizacion de pedido del cliente nombre:'.$customer->name_customer,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'actualizacion pedido'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['message'=>'Ok'
                        ,'pedido' => $cashOrder]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CashOrder  $cashOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //peticion para verificar la existencia del pedido
        $cashOrder = CashOrder::find($id);
        if (empty($cashOrder)) {
            return response(['Message'=>'Pedido no existe']);
        }
        //Eliminacion del pedido de forma logica
        $cashOrder->register_status_db_cashOrder = 1;
        $cashOrder->save();
        //Creacion del registro en el modulo de auditoria
        $datosAuditoria = ['description_aud'=> 'Eliminado de pedido numero: '.$cashOrder->id,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'borrado de pedido'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        //respuesta de la eliminacion del registro
        return response(['message' => 'Ok',
                         'CashOrder' => $cashOrder->id]);
    }
    public function addLoan($id){
        $cashOrder = CashOrder::find($id);
        if (empty($cashOrder)) {
            return response(['Message'=>'Pedido no existe']);
        }
        $validator = [
            'fk_id_cliente' => $cashOrder->fk_customer_id,
            'fk_id_cashOrder' => $cashOrder->id,
            'amount_loan' => $cashOrder->amount_cash_order,
            'amount_rest_loan' => $cashOrder->amount_cash_order,
            'debt_loan' => 0,
            'date_start_loan'=> date('Y-m-d'),
            'date_pay_loan'=> date('Y-m-d'),
            'interest_rate_loan'=>15
        ];
        $customer = Customers::where('id',$cashOrder->fk_customer_id)->where('register_status_db_customer',0)->first();
        if (empty($customer)) {
            return response(['Message'=> 'Cliente no existe'],404);
        }
        $ValidData=$validator;
        $loans = new Loans($ValidData);
        $loans->save();
        $datosAuditoria = ['description_aud'=> 'creacion de prestamo desde pedido para cliente:'.$customer->name_customer,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'creacion prestamo'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        $cashOrder->status_cash_order = 1;
        $cashOrder->save();
        return response(['message' => 'Ok',
                         'CashOrder' => $cashOrder->id]);
    }
}
