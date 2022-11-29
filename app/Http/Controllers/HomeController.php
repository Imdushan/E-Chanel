<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
       //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index() {
    //     $this->middleware('auth');
    //     if (!auth()->User()) {
    //         return redirect('/login');
    //     }else{
    //         return redirect('dashboard');
    //     }

    // }

    public function index() {
        $this->middleware('auth');
        // if (!auth()->user()){
        //     return redirect('/login');
        // }  else {
        //     return redirect('/login');
        // }
        if (Auth::user()->role == '') {
            return redirect('/');
        }elseif(Auth::user()->role == 'AS'){
            return redirect('/as/dashboard');
        }else{
            return redirect('login');
        }



    }

    public function dashboard(){
        return view('index');
    }

    public function userpage(){
        return view('index');
    }

    public function landinngPage(){
        return view('landing');
    }

}
