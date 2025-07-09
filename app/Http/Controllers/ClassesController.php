<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'Teacher') {
            // Only show classes assigned to this teacher
            $classes = Classes::where('teacher_id', $request->user->id)->get();
            $teachers = User::where('id', $request->user->id)->get(); // Only show current teacher
        } else {
            // Admin or other roles see all classes
            $classes = Classes::with('teacher')->get();
            $teachers = User::where('role', 'Teacher')->get();
        }

        return view('pages.classes.index', compact('classes', 'teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::all();

        return view('pages.classes.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required',
            'teacher_id' => 'required|exists:users,id'
        ]);

        Classes::create([
            'name' => $validated['name'],
            'teacher_id' => $validated['teacher_id']
        ]);

        return redirect()->route('classes.index')
        ->with('success', 'Student created succesfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Classes $class)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'teacher' && $request->has('teacher_classes')) {
            // Check if class is assigned to this teacher
            if ($class->teacher_id !== $request->user->id) {
                return redirect()->route('classes.index')
                    ->with('error', 'You do not have permission to view this class.');
            }
        }

        return view('pages.classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Classes $class)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'teacher' && $request->has('teacher_classes')) {
            // Check if class is assigned to this teacher
            if ($class->teacher_id !== $request->user->id) {
                return redirect()->route('classes.index')
                    ->with('error', 'You do not have permission to edit this class.');
            }

            // For teachers, only show themselves as an option
            $teachers = User::where('id', $request->user->id)->get();
        } else {
            // Admin or other roles see all teachers
            $teachers = User::all();
        }

        return view('pages.classes.edit', compact('class', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classes $class): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => 'required',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $updateData = [
            'nama_kelas' => $validated['nama_kelas'],
            'teacher_id' => $validated['teacher_id']
        ];

        $class->update($updateData);

        return redirect()->route('classes.index')
        ->with('success', 'Student updated succesfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Classes $class)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'teacher' && $request->has('teacher_classes')) {
            // Check if class is assigned to this teacher
            if ($class->teacher_id !== $request->user->id) {
                return redirect()->route('classes.index')
                    ->with('error', 'You do not have permission to delete this class.');
            }
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success','Class deleted successfully');
    }
}
