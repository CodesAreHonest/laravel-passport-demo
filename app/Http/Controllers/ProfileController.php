<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct() {
    }

    public function getProfiles (Request $request) {

        $profile = User::get(['name', 'email']);

        return response()->json ($profile, 200);
    }
}


