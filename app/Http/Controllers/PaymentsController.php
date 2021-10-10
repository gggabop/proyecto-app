<?php

namespace App\Http\Controllers;

use App\Http\Resources\Customer;
use App\Models\Audit;
use App\Models\Loans;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payments::where('register_status_db_payment',0)->get();
        if ($payments->isEmpty()) {
            return response(['Message'=>'No hay Pagos'],404);
        }
        return response(['message'=>'Ok','pagos'=>$payments],200);
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
    {

        $validator = Validator::make($request->all(), [
            'fk_id_loan' => 'required|numeric',
            'amount_payment' => 'required|numeric',
            'date_payment'=> 'required|date',
            'serial_payment'=> 'required|alpha_num'
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $loan = Loans::where('id',$request->fk_id_loan)->where('register_status_db_loan',0)->where('amount_rest_loan' !== 0)->first();
        if (empty($loan)) {
            return response(['Message'=> 'Prestamo no existe'],404);
        }
        $ValidData=$validator->validated();
        $variable = $loan->amount_rest_loan - $ValidData['amount_payment'];
        if ($variable >= 0) {
            $loan->amount_rest_loan = $variable;
            $loan->save();
        }else{
            return response(['errors' => 'Limite de pago excedido...']);
        }
        $payment = new Payments($ValidData);
        $payment->save();
        $datosAuditoria = ['description_aud'=> 'creacion de pago para pestamo:'.$loan->id,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'creacion pago'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['message'=>'Ok',
                         'data'=>$ValidData],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payments  $payments
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payments::where('id',$id)->where('register_status_db_payment',0)->get();
        if ($payment->isEmpty()) {
            return response(['Message'=>'Pago 404']);
        }
        $loan = Loans::where('id', $payment[0]['fk_id_loan'])->get();
        return response(['message'=>'Ok','pago' => $payment, 'prestamo' => $loan]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payments  $payments
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payments  $payments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $payment=Payments::find($id);
        if (empty($payment)) {
            return response(['Message'=>'Pago no existe'],404);
        }
        $validator = Validator::make($request->all(), [
            'fk_id_loan' => 'required|numeric',
            'amount_payment' => 'required|numeric',
            'date_payment'=> 'required|date',
            'serial_payment'=> 'required|alpha_num'
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $loan = Loans::where('id',$request->fk_id_loan)->where('register_status_db_loan',0)->first();
        if (empty($loan)) {
            return response(['Message'=> 'Prestamo no existe'],404);
        }
        $ValidData=$validator->validated();
        $payment->update($ValidData);
        $datosAuditoria = ['description_aud'=> 'actualizacion de pago para pestamo:'.$loan->id,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'actualizacion pago'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['message'=>'Ok',
                         'data'=>$ValidData],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payments  $payments
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payments::find($id);
        if (empty($payment)) {
            return response(['Message'=>'Pago no existe'],404);
        }
        $payment->register_status_db_payment = 1;
        $payment->save();
        $datosAuditoria = ['description_aud'=> 'Eliminado de pago serial: '.$payment->serial_payment,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'borrado de pago'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['message' => 'Ok',
                         'pago' => $payment->serial_payment]);
    }
}
