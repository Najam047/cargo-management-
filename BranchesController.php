<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use  Illuminate\Support\Facades\Auth;

class BranchesController extends Controller
{
    public function region()
    {
        $regions=Region::where('company_id',Auth::user()->company_id)->get();
        $i="";

        return view('region.index',compact('regions','i'));
    }

    public function addregion(Request $request)
    {
        $add=Region::create([
            'region_name'=>$request->input('region_name'),
            'company_id'=>$request->input('company_id'),
        ]);
        return back();
    }



}
