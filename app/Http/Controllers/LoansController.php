<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Customers;
use App\Models\Loans;
use App\Models\Payments;
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
        $loans = Loans::where('register_status_db_loan',0)->get();
        if ($loans->isEmpty()) {
            return response(['Message'=>'No hay Prestamos'],404);
        }
        return response(['message'=>'Ok','prestamos'=>$loans],200);
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
                'fk_id_cashorder' => 'numeric',
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
            $ValidData['amount_rest_loan'] = $ValidData['amount_loan'];
            $loans = new Loans($ValidData);
            $loans->save();
            $datosAuditoria = ['description_aud'=> 'creacion de prestamo para cliente:'.$customer->name_customer,
                                'fk_id_user'=>auth()->user()->id,
                                'action_aud'=>'creacion prestamo'];
            $auditoria = new Audit($datosAuditoria);
            $auditoria->save();
            return response(['message'=>'Ok',
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
        $loan = Loans::find($id);
        // if ($loan->isEmpty()) {
        //     return response(['Message'=>'Loan 404']);
        // }
        $customer=Customers::where('id',$loan->fk_id_cliente)->first();
        if (empty($customer)) {
            return response(['Message'=>'Cliente no existe']);
        }
        $pagos=Payments::where('fk_id_loan',$loan->id)->where('register_status_db_payment', 0)->get();
        return response(['message'=>'Ok',
                         'cliente'=>$customer,
                        'prestamo' => $loan,
                        'pagos'=>$pagos
                        ]);
// 'pagos'=>$pagos
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
        $Loan=Loans::find($id);
        if (empty($Loan)) {
            return response(['Message'=>'Prestamo no existe'],404);
        }
        $validator = Validator::make($request->all(), [
            'fk_id_cliente' => 'required|numeric',
            'fk_id_cashorder' => 'numeric',
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
        $customer = Customers::where('id',$Loan->fk_id_cliente)->first();
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
        return response(['message'=>'Ok','prestamo' => $Loan]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = Loans::find($id);
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
        return response(['message' => 'Ok',
                         'prestamo' => $loan->id]);
    }
}
