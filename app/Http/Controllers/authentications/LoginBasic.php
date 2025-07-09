<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function showLoginForm()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function login(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'password' => 'required',
      'email' => 'required|email|exists:users,email'
    ]);

    if ($validator->fails()) {
      $errors = $validator->errors();
      return back()->with('error', $errors->first());
    }

    $credentials = [
      'email' => $request->email,
      'password' => $request->password,
    ];

    if (Auth::attempt($credentials)) {
      // Check if user has allowed role (1 = Admin, 2 = Vendor)
      $user = Auth::user();

      if ($user->user_role == 1 || $user->user_role == 2) {
        return redirect('dashboard')->withSuccess('Logged in');
      } else {
        // Logout user if they don't have permission
        Auth::logout();
        return back()->with('error', 'Access denied. Only Admin and Vendor users can access this panel.');
      }
    } else {
      return back()->with('error', 'Email or Password does not match! please try again.');
    }
  }

  public function logout()
  {
    Session::flush();

    Auth::logout();

    return redirect('login')->withErrors('Logged out successfully');
  }
}
