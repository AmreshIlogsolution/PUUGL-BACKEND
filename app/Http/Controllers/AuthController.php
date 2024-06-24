<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CustomUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Validator;


class AuthController extends Controller
{
    public function register(Request $request){
      
      
        $messages = [
            'User Name and UID' => 'The :attribute field is required and does not allowed special characters or may be uID already exist',
            'Password' => 'The :attribute field is required and does not allowed special characters',
            'WareHouse Id' => 'The :attribute field is required and does not allowed special characters',
            'Department' => 'The :attribute field is required and does not allowed special characters',
         
        ];



        $validator = Validator::make($request->all(), [
            'uID' => 'required|string|unique:HHT_USER,uID',
            'uName' =>'required|string',
            'uPwd' =>'required|string',
            'uDept' =>'required|string',
            'uWH' =>'required|string',
            'WHID' =>'required|string',
            'uActive' =>'required|string',
            'password' =>'required|string',
            'user_type' =>'required|string',
        ], 
        $messages
    );




        if ($validator->fails()) {
            return response([          
                'responseStatus' =>'422',
                'responseMessage' => $messages,
                
            ],422);
        }



        $user = CustomUser::create([
            'uName'  =>  $request->uName,
            'uPwd' =>  $request->uPwd ,
            'uID' =>  $request->uID ,
            'uDept' =>  $request->uDept ,
            'uWH' =>  $request->uWH ,
            'WHID'=> $request->WHID  ,
            'uActive'=>  $request->uActive ,
            'password' => Hash::make($request->password),
            'user_type'=> $request->user_type,
        ]);


        $token = $user->createToken('min')->plainTextToken;
        
        return response([
            'user' => $user,
            'token' => $token,
        ]);

    }

    public function login(Request $request){ 
        
       
       
        $credentials = array(
            'uID' => $request->uID,
            'password' =>$request->password
        );
          
        $user = CustomUser::where('uID', $request->uID)->first();
       
    if ($user) {

       
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('min')->plainTextToken;
           $response = ['token' => $token];
           return response([
            'user' => $user,
            'token' => $token
        ]);
          //  return response($response, 200);
        } else {
            $response = ["error" => "Password mismatch"];
            return response($response, 422);
        }
    } else {
        $response = ["error" =>'User does not exist'];
        return response($response, 422);
    }


     //   $remember = $credentials['remember'] ?? false;
     //   unset($credentials['remember']);

        // if (Auth::attempt($credentials,true)) {
        //    print_r(Auth::user());dd();
           
        //    // $user = Auth::user();
            
        //     // $user = CustomUser::user();

        //     // $token = $user->createToken('main')->plainTextToken; 
          
            // return response([
            //     'user' => $user,
            //     'token' => $token
            // ]);

        //    // return response()->json(['message' => 'Login successful'], 200);
        // }else{
        //     return response([
        //         'error' => 'The Provided credentials are not correct'
        //     ], 422);
        // }
       
  
    }

    public function logout( ){
         /**@var User $user */
         
        $user = Auth::user();
     
         return response()->json($user);
       $user->currentAccessToken()->delete(); 
        return response([
            'success' =>true
        ]);
    }

}