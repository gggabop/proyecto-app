<?php

namespace App\Http\Controllers;

use App\Http\Resources\Customer;
use App\Models\Audit;
use App\Models\Customers;
use App\Models\Diary;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = Diary::where('register_status_db_diary',0)->get();
        if ($notes->isEmpty()) {
            return response(['Message'=>'No hay Notas'],404);
        }
        return response(['message'=>'Ok','notas'=>$notes],200);
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
        // Validacion
        $validator = Validator::make($request->all(), [
            'id_fk_customer' => 'numeric',
            'id_fk_loan' => array(Rule::requiredIf(!isset($request->id_fk_customer)),
                                  'numeric'),
            'note' => 'required',
            'type_note'=> 'required|numeric'
        ]);
        // Verifica si la validacion tira un error.
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        // Verifica si existe la llave foranea de clientes
        if ($request->id_fk_customer) {
            $customer = Customers::where('id',$request->id_fk_customer)->where('register_status_db_customer',0)->first();
              if (empty($customer)) {
                     return response(['Message'=> 'Cliente no existe'],404);
              }
            $dataDescAud = 'cliente: '.$customer->name_customer;
        }
        // Verifica si existe la llave foranea de prestamo en caso tal de no existir clientes
        if ($request->id_fk_loan) {
            $loan = Loans::where('id',$request->id_fk_loan)->where('register_status_db_loan',0)->first();
             if (empty($loan)) {
                 return response(['Message'=> 'Prestamo no existe'],404);
            }
            $dataDescAud = 'pretamo: '.$loan->id;
        }
        // Establece los datos validados a una variable para ingresarlos a la base de datos
        $ValidData=$validator->validated();
        $note = new Diary($ValidData);
        $note->save();
        $datosAuditoria = ['description_aud'=> 'creacion de nota para '.$dataDescAud,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'creacion nota agenda'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        // Envia una respuesta
        return response(['message'=>'Ok',
                         'nota'=>$ValidData],200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Diary  $diary
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $note = Diary::where('id',$id)->where('register_status_db_diary',0)->first();
        if (empty($note)) {
            return response(['Message'=>'Note 404'],404);
        }
        $customer = Customers::where('id', $note->id_fk_customer)->first();
        if ($note->id_fk_loan){
            $loan = Loans::where('id', $note->id_fk_customer)->first();
            return response(['message'=>'Ok','nota' => $note, 'cliente' => $customer, 'prestamo' => $loan]);
        }
        return response(['message'=>'Ok','nota' => $note, 'cliente' => $customer]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Diary  $diary
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Diary  $diary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Peticion para verificar que la nota existe
        $note = Diary::where('id',$id)->where('register_status_db_diary',0)->first();
        if (empty($note)) {
            return response(['Message'=>'Note 404'],404);
        }
        // Validacion
        $validator = Validator::make($request->all(), [
            'id_fk_customer' => 'numeric',
            'id_fk_loan' => array(Rule::requiredIf(!isset($request->id_fk_customer)),
                                  'numeric'),
            'note' => 'required',
            'type_note'=> 'required|numeric'
        ]);
        // Verifica si la validacion tira un error.
        if ($validator->stopOnFirstFailure()->fails()){
            return response(['errors' => $validator->errors()]);
        }
        // Verifica si existe la llave foranea de clientes
        if ($request->id_fk_customer) {
            $customer = Customers::where('id',$request->id_fk_customer)->where('register_status_db_customer',0)->first();
              if (empty($customer)) {
                     return response(['Message'=> 'Cliente no existe'],404);
              }
            $dataDescAud = 'cliente: '.$customer->name_customer;
        }
        // Verifica si existe la llave foranea de prestamo en caso tal de no existir clientes
        if ($request->id_fk_loan) {
            $loan = Loans::where('id',$request->id_fk_loan)->where('register_status_db_loan',0)->first();
             if (empty($loan)) {
                 return response(['Message'=> 'Prestamo no existe'],404);
            }
            $dataDescAud = 'pretamo: '.$loan->id;
        }
        // Establece los datos validados a una variable para ingresarlos a la base de datos
        $ValidData=$validator->validated();
        $note->update($ValidData);
        $datosAuditoria = ['description_aud'=> 'actualizacion de nota para '.$dataDescAud,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'actualizacion nota agenda'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        // Envia una respuesta
        return response(['message'=>'Ok',
                         'nota'=>$note],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Diary  $diary
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $note = Diary::where('id',$id)->where('register_status_db_diary',0)->first();
        if (empty($note)) {
            return response(['Message'=>'Note 404'],404);
        }
        $note->register_status_db_diary = 1;
        $note->save();
        $datosAuditoria = ['description_aud'=> 'Eliminado de nota de agenda numero: '.$note->id,
                            'fk_id_user'=>auth()->user()->id,
                            'action_aud'=>'borrado de agenda'];
        $auditoria = new Audit($datosAuditoria);
        $auditoria->save();
        return response(['message' => 'Ok',
                         'nota' => $note->id]);
    }
}
