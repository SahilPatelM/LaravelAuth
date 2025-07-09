<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
  //
  public function index()
  {
    return view('users.index');
  }

  public function list()
  {
    return DataTables::of(User::query())->make(true);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:50',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:6',
      'user_role' => 'required|in:1,2,3,4'
    ]);

    // Create the vendor
    $vendor = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      'user_role' => $request->user_role,
      'is_selling_tea' => $request->is_selling_tea = '1' ? 1 : 0,
    ]);

    return response()->json(['success' => true, 'vendor' => $vendor, 'message' => 'User created successfully!']);
  }

  public function edit($id)
  {
    $category = User::findOrFail($id);
    return response()->json($category);
  }

  public function update(Request $request, $id)
  {
    $request->validate([
      'name' => 'required|string|max:50',
      'email' => 'required|email|unique:users,email,' . $id,
      'user_role' => 'required|in:1,2,3,4'
    ]);

    $user = User::findOrFail($id);
    $user->name = $request->name;
    $user->email = $request->email;
    $user->user_role = $request->user_role;

    if ($request->has('password') && !empty($request->password)) {
      $user->password = bcrypt($request->password);
    }

    if ($request->has('is_selling_tea')) {
      $user->is_selling_tea = $request->is_selling_tea ? 1 : 0;
    }

    $user->save();

    return response()->json(['success' => true, 'message' => 'User updated successfully!']);
  }

  // In UserDestroy
  public function destroy(Request $request)
  {
    $user = User::findOrFail($request->id);
    $user->delete();

    return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
  }
}
