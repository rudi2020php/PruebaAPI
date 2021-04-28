<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        return "it's works!!! -- IN STORE, Auth";
    }

    public function signin(Request $request){
        return "it's works!!!-- IN SIGNIN, Auth";
    }
}
