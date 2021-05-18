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
use App\Models\Shipment;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Account;

class ShipmentController extends Controller
{




    public function index ()
    {
        $clients=array();
        //$shipment_list = [];
        $keys=array('id','name');
        $Users=DB::table('users')-> join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        -> join('roles', 'model_has_roles.role_id', '=', 'roles.id')->where('roles.name','User')->where('users.company_id',Auth::user()->company_id)->where('region_id',Auth::user()->region_id)->get();
        foreach($Users as $mail)
        {
            $data=User::where('email',$mail->email)->first();
            array_push($clients,array_combine($keys,[$data->id,$data->name]));
        }
        $products=Product::where('company_id',Auth::user()->company_id)
        ->where('region_id',Auth::user()->region_id)->get();

        $shipments = Shipment::all();


      //  for($i=1;$i<=count($shipments);$i++){

        //    $shipment_list = DB::table('shipment')
        //    ->join('users', json_decode((int)$shipments[0]->client_id), '=', 'users.id')
        //     // ->join('products', json_decode((int)$shipments[0]->product_id), '=', 'products.id')
        //     ->where(json_decode((int)$shipments[0]->company_id),Auth::user()->company_id)
        //     ->where(json_decode((int)$shipments[0]->region_id),Auth::user()->region_id);


        $shipment_list =DB::table('shipment')->join('users', 'shipment.client_id', '=', 'users.id')
        //->join('products', 'shipment.product_id', '=', 'products.id')
        ->where('shipment.company_id',Auth::user()->company_id)->where('shipment.region_id',Auth::user()->region_id)->get();

//    }

        $banks=Account::where('company_id',Auth::user()->company_id)->get();


        return view('shipment.index',compact('clients','products','shipment_list','banks'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    public function create()
    {
        return view('shipment.create');
    }
    public function store(Request $request)
    {

        // request()->validate([



        // 'shippingdate' => 'required',
        // 'containernum'=> 'required',
        // 'biltynum'=> 'required',
        // 'qty'=> 'required',
        // 'product_id'=> 'required',
        // 'length'=> 'required',
        // 'height'=> 'required',
        // 'width'=> 'required',
        // 'weight'=> 'required',
        // 'shipping'=> 'required',
        // 'tax'=> 'required',
        // 't_fair'=> 'required',
        // ]);
        // dd($request->all());
        $add=Shipment::create([
            'company_id'=>$request->input('company_id'),
            'client_id'=>$request->input('client_id'),
            'region_id'=>Auth::user()->region_id,
            'product_id'=>json_encode($request->input('product_id')),
            'shippingdate'=>$request->input('shippingdate'),
            'containernum'=>$request->input('containernum'),
            'biltynum'=>$request->input('biltynum'),
            'qty'=> json_encode($request->input('qty')),
            'details'=>$request->input('details'),
            'length'=>$request->input('length'),
            'height'=>$request->input('height'),
            'width'=>$request->input('width'),
            'weight'=>$request->input('weight'),
            'shipping'=>$request->input('shipping'),
            'tax'=>$request->input('tax'),
            'fair'=>json_encode($request->input('fair')),
            'unit'=>json_encode($request->input('unit')),
            't_fair'=>$request->input('x'),
            'remaning_amount'=>$request->input('x'),
            'status'=>$request->input('status'),

        ]);

        $payadd=Payment::create([
            'company_id'=>$request->input('company_id'),
            'client_id'=>$request->input('client_id'),
            'region_id'=>Auth::user()->region_id,
            'shipment_id'=>$add->id,
            'pay_date'=>$request->input('shippingdate'),
            'pay_op'=>'-',
            'amount'=>$request->input('x'),
            'status'=>'0',
            'description'=>'INV#'.$add->id,
        ]);

        return redirect()->route('shipment.index')
                        ->with('success','Shipment added successfully.');
    }


    public function edit($id)

    {
        $shipment = Shipment::where('shipment_id',$id)->first();
        $client=User::where('id',$shipment->client_id)->first();
        $product=Product::where('id',$shipment->product_id)->first();

        return view('shipment.edit',compact('shipment','client','product'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'shippingdate' => 'required',
            'containernum'=> 'required',
            'biltynum'=> 'required',
            'qty'=> 'required',
            'product'=> 'required',
            'length'=> 'required',
            'height'=> 'required',
            'width'=> 'required',
            'weight'=> 'required',
            'shipping'=> 'required',
            'tax'=> 'required',
            't_fair'=> 'required',
            ]);

            $input = $request->all();

            $shipment = Shipment::where('shipment_id',$id)->first();
            $shipment->update($input);




            return redirect()->route('shipment.index')

                        ->with('success','Shipment updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shipment $shipment
     * @return \Illuminate\Http\Response
     */


    public function destroy($id)
    {

      $shipment= Shipment::where('shipment_id',$id)->first();
        return redirect()->route('shipment.index')
                        ->with('success','Shipment deleted successfully');
    }

    public function shipmentdelete($id)
    {

        Shipment::where('shipment_id','=',$id)->delete();
        return redirect()->route('shipment.index')
                        ->with('success','Shipment deleted successfully');


    }






    public function shipmentledger($id)
    {
        $shipmentledgers=Payment::where('shipment_id',$id)->get();

        $i=1;
        return view('shipment.shipmentledger',compact('shipmentledgers','i'));
    }







}
