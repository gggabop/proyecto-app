<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Customers;
use App\Models\loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = loans::where('register_status_db_loan',0)->get();
        if ($loans->isEmpty()) {
            return response(['Message'=>'No hay Prestamos'],404);
        }
        return response(['Pedidos'=>$loans],200);
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
                'fk_id_cliente' => 'required|numeric',
                'fk_id_cashOrder' => 'numeric',
                'amount_loan' => 'required|numeric',
                'date_start_loan'=> 'required|date',
                'date_pay_loan'=> 'required|date',
                'interest_rate_loan'=>'required|integer'
            ]);
            if ($validator->stopOnFirstFailure()->fails()){
                return response(['errors' => $validator->errors()]);
            }
            $customer = Customers::where('id',$request->fk_id_cliente)->where('register_status_db_customer',0)->first();
            if (empty($customer)) {
                return response(['Message'=> 'Cliente no existe'],404);
            }
            $ValidData=$validator->validated();
            $loans = new loans($ValidData);
            $loans->save();
            $datosAuditoria = ['description_aud'=> 'creacion de prestamo para cliente:'.$customer->name_customer,
                                'fk_id_user'=>auth()->user()->id,
                                'action_aud'=>'creacion prestamo'];
            $auditoria = new Audit($datosAuditoria);
            $auditoria->save();
            return response(['Message'=>'Prestamo Agregado',
                             'Data'=>$ValidData],200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = loans::where('id',$id)->where('register_status_db_loan',0)->get();
        if ($loan->isEmpty()) {
            return response(['Message'=>'Loan 404']);
        }
        return response(['Prestamo' => $loan]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\loans  $loans
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Loan=loans::find($id);
        if (empty($Loan)) {
            return response(['Message'=>'Prestamo no existe'],404);
        }
        $validator = Validator::make($request->all(), [
            'fk_id_cliente' => 'required|numeric',
            'fk_id_cashOrder' => 'numeric',
            'amount_loan' => 'required|numeric',
            'amount_rest_loan' => 'numeric',
            'debt_loan' => 'numeric',
            'date_start_loan'=> 'required|date',
            'date_pay_loan'=> 'required|date',
            'interest_rate_loan'=>'required|integer'
        ]);
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        $customer = Customers::where('id',$Loan->fk_id_cliente)->where('register_status_db_customer',0)->first();
        if (empty($customer)) {
            return response(['Message'=> 'Cliente no existe'],404);
        }
        $ValidData=$validator->validated();
        $Loan->update($ValidData);
        $datosAuditoria = ['description_aud'=> 'actualizacion de prestamo para cliente:'.$customer->name_customer,
                                'fk_id_user'=>auth()->user()->id,
                                'action_aud'=>'Actualizacion prestamo'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['Prestamo' => $Loan]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = loans::find($id);
        if (empty($loan)) {
            return response(['Message'=>'Prestamo no existe'],404);
        }
        $loan->register_status_db_loan = 1;
        $loan->save();
        $datosAuditoria = ['description_aud'=> 'Eliminado de prestamo numero: '.$loan->id,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'borrado de prestamo'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['Message' => 'Deleted Loan',
                         'Loan' => $loan->id]);
    }
}
