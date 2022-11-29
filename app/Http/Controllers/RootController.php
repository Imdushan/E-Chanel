<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\Hello;

class RootController extends Controller
{
    public function dashboard(REQUEST $request){
       // $track="Inesh";
       //event(new Hello('Inesh'));
        return view('dashboard');
    }
    public function login(REQUEST $request){
         return view('login');
     }
     public function userdashboard(REQUEST $request){
        // $track="Inesh";
        //event(new Hello('Inesh'));
         return view('userdashboard');
     }

    public function hello(REQUEST $request){
        event(new Hello('hello world'));
     }

    public function index(Request $request){
        return view('index');
    }
}
