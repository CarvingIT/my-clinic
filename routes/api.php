<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function(Request $request){
   if (auth()->attempt(['email'=>$request->email, 'password'=>$request->password])) {
        return ['status'=>'successful'];
   } 
   else{
        return ['status'=>'failure'];
   }
});
