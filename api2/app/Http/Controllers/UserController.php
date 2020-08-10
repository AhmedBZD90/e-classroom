<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Validator;

class UserController extends Controller
{
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (\Request::is('api*')) {

            $users = DB::table('users')
                ->join('model_has_roles','model_has_roles.model_id','=','users.id')
                ->join('roles','roles.id','=','model_has_roles.role_id')
                ->select('users.id',
                        'users.name',
                        'users.email',
                        'users.created_at',
                        'roles.name as role',
                        'roles.id as role_id')
                ->get();
            
            
           return response()->json($users, 200);

        }else{

            $data = User::orderBy('id','DESC')->paginate(5);
            return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
        }
        
    }


    public function roles()
    {
        $roles = Role::pluck('name','name')->all();

        return response()->json($roles, 200);
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {        
        if (\Request::is('api*')) {
             // validator
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'roles' => 'required'
            ], [
                'name.required' => 'Please enter a username',
                'email.required' => 'Please enter an e-mail',
                'email.email' => 'Please enter a valid e-mail',
                'email.unique:users' => 'E-mail already used, please to try with another one',
            ]);

            // check validation
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->messages()
                ];
                return response()->json($response, 404);
            }

            // try to store the book
            try {
                $input = $request->all();
                $input['password'] = Hash::make($input['password']);

                $user = User::create($input);
                $user->assignRole($request->input('roles'));
                $success = true;
                $message = "Great success! New User created successfully";
            } catch (\Illuminate\Database\QueryException $ex) {
                $success = false;
                $data = null;
                $message = $ex->getMessage();
            }

            // make response
            $response = [
                'success' => $success,
                'message' => $message
            ];

            // return response
            return response()->json($response, 200);

        } else {
            $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
            ]);

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            $user->assignRole($request->input('roles'));
            return redirect()->route('users.index')->with('success','User created successfully');
        }  
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
        if (\Request::is('api*')) {
            if (! $user){
                return response()->json(['error' => 'User Not Found'], 404);
            }
            
            $userRole = $user->roles->pluck('name')->all();
            
            return response()->json(['user' => $user,
                                     'role' => $userRole] , 200);

        } else {
            return view('users.show',compact('user'));
        }
        
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
        if (\Request::is('api*')) {

            // validator
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
            ], [
                'name.required' => 'Please enter a username',
                'email.required' => 'Please enter an e-mail',
                'email.email' => 'Please enter a valid e-mail',
                'email.unique:users' => 'E-mail already used, please to try with another one',
            ]);

            // check validation
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->messages()
                ];
                return response()->json($response, 404);
            }

            // try to store the book
            try {
                $name = $request['name'];
                $email = $request['email'];
                $password = $request['password'];
                $role = $request['role'];
                
                $user = User::find($id);

                $user->name = $name;
                $user->email = $email;
                $user->password = bcrypt($password);
                
                DB::table('model_has_roles')->where('model_id',$id)->delete();
                $user->assignRole($role);

                $user->save();

                $success = true;
                $message = "Great success! User Updated successfully";
            } catch (\Illuminate\Database\QueryException $ex) {
                $success = false;
                $data = null;
                $message = $ex->getMessage();
            }

            // make response
            $response = [
                'success' => $success,
                'message' => $message
            ];

            // return response
            return response()->json($response, 200);
        } else {
            $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
           
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{

            $input = array_except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));
        
           
            return redirect()->route('users.index')
                            ->with('success','User updated successfully');
        
        }
        
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

         if (\Request::is('api*')) {
             return response()->json([
                'message' => 'Great success! User deleted successfully'
            ]);
         } else {
           return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
         }         
    }


    
}
