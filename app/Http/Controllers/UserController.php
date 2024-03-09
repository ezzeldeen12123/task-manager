<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index(Request $request) {

        $user = auth()->user();
        return 'here';
    }
    
    public function show(Request $request, User $user) {

        $user->photo = $user->urlFile();
        return $user;
    }

    public function update(Request $request, User $user) {

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if($request->photo) {
            $user->saveFile($request->photo);
        }
        return response()->json(['user' => $user, 'message' => 'Updated Successfully', 'status' => true]);
    }

    public function changePassword(Request $request, User $user) {

        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => '', 'status' => true], 200);
    }
}
