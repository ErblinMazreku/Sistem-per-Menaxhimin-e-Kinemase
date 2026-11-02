<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create() { return view('auth.register'); }
    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'unique:users'], 'password' => ['required', 'confirmed', 'min:8']]);
        $user = User::create($data + ['role' => 'customer']);
        Auth::login($user); $request->session()->regenerate();
        return redirect()->route('home');
    }
}
