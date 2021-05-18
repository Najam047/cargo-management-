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
//use Illuminate\Support\Facades\Hash as FacadesHash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $roleName = Auth::user()->roles->pluck('name');
        if($roleName[0] == 'region-admin'){
            $data = User::where('region_id',Auth::user()->region_id)
            ->where('id','!=',Auth::user()->id)
            ->get();



            return view('users.index',compact('data'));
        }

        $data = User::all()->where('id','!=',1)->where('company_id',Auth::user()->company_id)->where('id','!=',Auth::user()->id);
        
        return view('users.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {




    }
    public function create()
    {

        $roleName = Auth::user()->roles->pluck('name');
        if($roleName[0] == 'region-admin'){
            $regions = Region::where('id',Auth::user()->region_id)->first();
            $roles = Role::where('name','User')->first();

            return view('users.create',compact('regions','roles'));
        }
        $regions=Region::where('company_id',Auth::user()->company_id)->get();

        $roles = Role::where('company_id',Auth::user()->company_id)-> pluck('name','name')->all();

        return view('users.create',compact('roles','regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $this->validate($request, [
            'name' => 'required',
            'region_id' => 'required',
            'address' => 'required',
            // 'email' => 'required|email|unique:users,email',
            // 'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        //$input = $request->all();
        $company_id=Auth::user()->company_id;
        $password = Hash::make($request->password);

        //$user = User::create($input);
        $user = new User();
        $user->company_id = $company_id;
        $user->name = $request->name;
        $user->region_id = $request->region_id;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->password = $password;
        $user->save();

        $user->assignRole($request->input('roles'));


        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            // 'email' => 'required|email|unique:users,email,'.$id,
            // 'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

    public function clientdelete($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

    public function users(){
        return $users =user::all();
    }

    public function getUser($id){
        return $user =User::find($id);
    }

   // public function getCompanyUsers($id){
      //  return $user =User::find($id);
   // }

   public function deletetUser($id){
     $user =User::find($id);
     $user->delete();
     return ['message'=> 'user deleted successfully'];


}

public function updateUser(Request $request){
    $user =User::find($request->id);
    $user->name =$request->name;
    return ['message'=> 'user record updated successfully'];
 }

}
