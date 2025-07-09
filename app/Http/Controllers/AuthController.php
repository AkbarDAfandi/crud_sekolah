<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function loginUser(Request $request)
    {
        // Validate user request
        $request->validate([
            'email'=>'required|email:users',
            'password'=>'required'
        ]);

        // Get user data
        $user = User::where('email','=',$request->email)->first();
        if($user) {
            if(Hash::check($request->password, $user->password)){
                $request->session()->put('loginId', $user->id);
                $request->session()->put('user', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
                return redirect('/');
            } else {
                return back()->with('fail','Password not match!');
            }
        } else {
            return back()->with('fail','This email is not register.');
        }
    }

    public function dashboard()
    {
        // Initialize data array
        $data = array();
        $teachers = User::where('role','=','Teacher')->get();
        $students = Student::all();
        $subjects = Subject::all();
        $classes = Classes::all();

        if(Session::has('loginId')){
            $data = User::where('id','=',Session::get('loginId'))->first();
            // Ensure session has user data with role
            if (!Session::has('user.role')) {
                Session::put('user', [
                    'id' => $data->id,
                    'name' => $data->name,
                    'email' => $data->email,
                    'role' => $data->role
                ]);
            }
        }
        return view('pages.dashboard',compact('data','teachers','students','subjects','classes'));
    }

    public function logout()
    {
        // $data = array();
        if(Session::has('loginId')){
            Session::pull('loginId');
            return redirect('/');
        }
    }

    public function profile()
    {
        $user = User::find(Session::get('loginId'));
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.Session::get('loginId')
        ]);

        $user = User::find(Session::get('loginId'));
        $user->name = $request->name;
        $user->email = $request->email;

        if($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }
}
