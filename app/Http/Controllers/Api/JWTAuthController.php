<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use JWTAuth;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class JWTAuthController extends Controller
{
    public $token = true;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
  
    public function register(Request $request)
    {
         $validator = Validator::make($request->all(),[ 
                      'name' => 'required',
                      'email' => 'required|string|email|unique:users',
                      'password' => 'required|confirmed|min:6',   
                      'phoneNo' => 'required|integer',
                      'image' => 'required|file'
                     ]);  
         if ($validator->fails()) {  
               return response()->json($validator->errors()->toJson(), 401 ); 
         }   
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->phoneNo = $request->phoneNo;
        $profileImage = $request->file('image');
        $profileImageSaveAsName = time() . "-profile." . $profileImage->getClientOriginalExtension();
        $upload_path = 'profile_images/';
        $profile_image_url = $upload_path . $profileImageSaveAsName;
        $success = $profileImage->move($upload_path, $profileImageSaveAsName);
        $user->image =  $profile_image_url;
        $user->save();
        if ($this->token) {
            return $this->login($request);
        }
        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }
  
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }
  
    public function logout()
    {

        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
       
    }
  
    public function getUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
  
        $user = JWTAuth::authenticate($request->token);
  
        return response()->json(['user' => $user]);
    }
}
