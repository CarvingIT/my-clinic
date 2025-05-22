<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function(Request $request){
   if (auth()->attempt(['email'=>$request->email, 'password'=>$request->password])) {
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('app-token');
        // generate token
        return ['status'=>1, 'token'=>$token->plainTextToken];
   } 
   else{
        return ['status'=>0];
   }
});
