<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Region;
use App\Models\Shipment;
use App\Models\Payment;
use App\Models\Company;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $var= User::where('email','=',NULL)->get();
        $clientCount = count($var);


        $ship=Shipment::all();
        $shipCount= count($ship);

        $earn=Payment::all()->sum('amount');

        $reg=Region::all();
        $regionCount= count($reg);

        $com=Company::all();
        $comCount= count($com);

        $var= User::where('region_id','=',NULL)->get();
        $adminCount = count($var);


        $regionname=Region::where('id',Auth::user()->region_id)->first();
        $roles = Role::where('name','User')->first();
        return view('admin.dashboard',compact('regionname','clientCount' ,'shipCount' ,'earn' , 'regionCount','comCount','adminCount'));
    }




}
