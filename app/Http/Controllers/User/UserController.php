<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //step01
            $users = User::all();

     //return response()->json(['data'=> $users], 200);
     return $this->showAll($users);
      // return $users;


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function create()
    {
        //
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //step-03
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',

        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        //return response()->json(['data'=>$user], 201);
        return $this->showOne($user, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //step02

        // $user = User::findOrFail($id);

         //return response()->json(['data'=>$user], 200);
         return $this->showOne($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, User $user)
    {
        //STEP-04
       //  $user = User::findOrFail($id);

         $rules = [
            'email' => 'email|unique::users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,

         ];

         if($request->has('name')){
            $user->name = $request->name;
         }

         if($request->has('email') && $user->email != $request->email){
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;

         }

         if($request->has('password')){
            $user->password = bcrypt($request->password);
         }

         if($request->has('admin')){
            if(!$user->isVerified()){
              //  return response()->json(['error' => 'Only verified users can modify the admin field', 'code'=>409], 409);
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }
            $user->admin = $request->admin;

         }
         if(!$user->isDirty()){
          //  return response()->json(['error'=> 'You need to specify a different value to update', 'code'=>422], 422);
            return $this->errorResponse('You need to specify a different value to update', 422);
         }

         $user->save();
         //return response()->json(['data' => $user], 200);
         return $this->showOne($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
           // $user = User::findOrFail($id);

            $user->delete();
            //return response()->json(['data' => $user], 200);
            return $this->showOne($user);


    }
}
