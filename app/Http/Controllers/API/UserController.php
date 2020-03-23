<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $this->authorize('isAdmin');
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {

            return User::latest()->paginate(5);

        }


        // return User::all();
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
            'name' => 'required|string|max:191',
            'email' => 'required|string|max:191|email|unique:users',
            'password' => 'required|string|min:8',
            'type' => 'required|string'
        ]);


        // return $request->all();
        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'password' => Hash::make($request['password']),
            'bio' => $request['bio'],
            'photo' => $request['photo'],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'sometimes|string|max:191|email',
            'password' => 'sometimes|string|min:8',
            'file' => 'mimes:jpeg,jpg,png,gif|max:2111775'
        ]);
        $currentPhoto =  $user->photo;

        if($request->photo != $currentPhoto){

            $name = time().'.'.explode('/' , explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];

            \Image::make($request->photo)->save(public_path('images/profile/').$name);

            // $request->photo = $name;
            $request->merge(['photo' => $name]);

            $userPhoto=public_path('images/profile/').$currentPhoto;

            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }

        }
        if(!empty($request->password)){
            $password =Hash::make($request->password);
            $request->merge(['password' => $password]);
        }


        $user->update($request->all());
        return ['message' => 'success'];
    }

    public function profile()
    {
        return auth('api')->user();
    }


        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|max:191|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:8',
            'type' => 'required|string'
        ]);

        $user->update($request->all());

        return ['message' => 'updated info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $this->authorize('isAdmin');

        $user = User::find($id)->delete();

        return ['message' => 'User Deleted'];
    }

    public function search()
    {
        if($search =\Request::get('q')){
            $users=User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                      ->orWhere('email','LIKE',"%$search%")
                      ->orWhere('type','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $users = User::paginate(10);
        }

        return $users;
    }
}
