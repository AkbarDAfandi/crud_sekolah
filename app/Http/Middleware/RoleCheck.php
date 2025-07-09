<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use App\Models\Student;
use App\Models\Classes;


class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (!Session::has('loginId')) {
            return redirect('login')->with('fail', 'You must be logged in');
        }

        // Get user from session
        $user = \App\Models\User::find(Session::get('loginId'));

        // If user is not found, redirect to login
        if (!$user) {
            return redirect('login')->with('fail', 'User not found');
        }

        // Store user in session for views
        Session::put('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ]);

        // Store user in request for controllers to access
        $request->merge(['user' => $user]);

        // If user is admin, allow access to everything
        if ($user->role === 'Admin') {
            return $next($request);
        }

        // If user is teacher, restrict access
        if ($user->role === 'Teacher') {
            // Block access to teachers tab
            if ($request->is('teachers*')) {
                return redirect()->route('dashboard')->with('fail', 'You do not have permission to access this page.');
            }

            // Allow access to dashboard
            if ($request->is('dashboard')) {
                return $next($request);
            }

            // Allow access to profile routes
            if ($request->is('profile*')) {
                return $next($request);
            }

            // For classes, only allow access to assigned classes
            if ($request->is('classes*')) {
                $teacherClasses = Classes::where('teacher_id', $user->id)->pluck('id')->toArray();
                if (empty($teacherClasses)) {
                    return redirect()->route('dashboard')->with('fail', 'You are not assigned to any classes.');
                }

                // If this is a specific class view, check if teacher is assigned to it
                $classId = $request->route('class') ?? $request->route('id');
                if ($classId && !in_array($classId, $teacherClasses)) {
                    return redirect()->route('dashboard')->with('fail', 'You do not have access to this class.');
                }

                $request->merge(['teacher_classes' => $teacherClasses]);
                return $next($request);
            }

            // For students, only allow access to students in their classes
            if ($request->is('students*')) {
                $teacherClassIds = Classes::where('teacher_id', $user->id)->pluck('id')->toArray();
                if (empty($teacherClassIds)) {
                    return redirect()->route('dashboard')->with('fail', 'You are not assigned to any classes.');
                }


                // If this is a specific student view, check if they're in teacher's classes
                $studentId = $request->route('student') ?? $request->route('id');
                if ($studentId) {
                    $student = Student::where('id', $studentId)->first();
                    if ($student && !in_array($student->class_id, $teacherClassIds)) {
                        return redirect()->route('dashboard')->with('fail', 'You do not have access to this student.');
                    }
                }

                $request->merge(['teacher_class_ids' => $teacherClassIds]);
                return $next($request);
            }

            // Block all other routes except GET requests to allowed paths
            // if (!$request->isMethod('get')) {
            //     return redirect()->back()->with('fail', 'You do not have permission to perform this action.');
            // }

            // Default: allow access for now, can be restricted further as needed
            return $next($request);
        }

        // Default: allow access for other roles
        return $next($request);
    }
}
