<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;


class LoginController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
    public function index(){
        try{


            $user = auth('sanctum')->user()->id;
            $userdetails = User::where('id', $user)->first();

            $userreturn = [
                'Name' => $userdetails->name,
                'Email' => $userdetails->email,
                'Role' => $userdetails->role,
            ];

            if ($user == null){
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            }
            return $this->sendResponse($userreturn, 'User retrieved successfully.');
        }catch (\Exception $e){
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'c_password' => 'required|string|min:6|same:password',
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
    public function logout(){
        try{
            auth()->logout();
            auth('sanctum')->user()->tokens()->delete();
            return $this->sendResponse([], 'Successfully logged out.');
        }
        catch (\Exception $exception){
            return $this->sendResponse([], $exception);
        }

    }

}
