<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use  Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use  Illuminate\Support\Facades\Hash;
use  Illuminate\Support\Facades\Auth;
use App\Models\Region;
use App\Models\Account;
use App\Models\Payment;

class AccountController extends Controller
{
    public function index(){

        // $Accounts = Account::where('company_id',Auth::user()->company_id)->where('id','!=',Auth::user()->id);
        // $Accounts = Account::where('company_id',Auth::user()->company_id)->where('region_id',Auth::user()->region_id)->paginate(5);
        // return view('account.index',compact('Accounts'))
        //     // ->with('i', (request()->input('page', 1) - 1) * 5);

        $accounts=Account::where('company_id',Auth::user()->company_id)->get();

            $accountdata=array();
            $keys=array('id','bank_name','balance');
            foreach($accounts as $acc)
            {

                $totaladd=Payment::where('b_id',$acc->bank_id)->where('company_id',Auth::user()->company_id)->where('pay_op','+')->sum('amount');
                $totalexpense=Payment::where('b_id',$acc->bank_id)->where('company_id',Auth::user()->company_id)->where('pay_op','-')->sum('amount');
                $balance=$totaladd-$totalexpense;
                array_push($accountdata,array_combine($keys,array($acc->bank_id,$acc->bank_name,$balance)));
            }
            $i=1;


            return view('account.index',compact('accountdata','i'));
            //     // ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function addaccount(Request $request)
    {
        $add=Account::create([
            'bank_name'=>$request->input('bank_name'),
            'region_id'=>Auth::user()->region_id,
            'company_id'=>Auth::user()->company_id,

        ]);


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


        // Account::create($request->all());

        return redirect()->route('account.index')
                        ->with('success','Account created successfully.');
    }
    public function ledger($id)
    {
        $ledgers=Payment::where('client_id',$id)->get();
        $i=1;
        return view('users.ledger',compact('ledgers','i'));
    }



    public function balancesheet($id)
    {
        $bankdata=Payment::where('b_id',$id)->get();

        $i=1;
        return view('account.balancesheet',compact('bankdata','i'));
        // return view('account.balancesheet');
    }
    public function addtransaction(Request $request)
    {
    //        $transaction=Payment::where('b_id','=',$id)->('company_id','=',Auth::user()->company_id)->get();

    $transaction=Payment::create([
        'b_id'=>$request->input('bank_id'),
        'pay_date'=>$request->input('pay_date'),
        'description'=>$request->input('details'),
        'pay_op'=>$request->input('pay_op'),
        'amount'=>$request->input('amount'),
        'company_id'=>Auth::user()->company_id,
        'status'=>'0',


    ]);
    return back();

   }
   public function comapanyledger()
   {
    //    $comapanyledger=Payment::where('company_id',Auth::user()->company_id)->where('pay_op','+')->get();
    $comapanyledger =DB::table('payment')->join('users', 'payment.client_id', '=', 'users.id')
    //->join('products', 'shipment.product_id', '=', 'products.id')
    ->where('payment.company_id',Auth::user()->company_id)->where('payment.pay_op','+')->get();

       $i=1;
       return view('account.company',compact('comapanyledger','i'));
   }

}
