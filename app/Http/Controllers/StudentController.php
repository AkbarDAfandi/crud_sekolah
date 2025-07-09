<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Exports\StudentExport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Classes;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'Teacher') {
            // Get teacher's class IDs
            $teacherClassIds = \App\Models\Classes::where('teacher_id', $request->user->id)
                ->pluck('id')
                ->toArray();

            if (empty($teacherClassIds)) {
                return redirect()->route('dashboard')
                    ->with('fail', 'You are not assigned to any classes.');
            }

            // Only show students in classes assigned to this teacher
            $students = Student::whereIn('class_id', $teacherClassIds)
                ->with('class') // Eager load the class relationship
                ->orderBy('class_id', 'asc')
                ->get();


            $classes = Classes::whereIn('id', $teacherClassIds)->get();
        } else {
            // Admin or other roles see all students
            $students = Student::with('class')->get();
            $classes = Classes::orderBy('name', 'asc')->get();
        }

        return view('pages.students.index', compact('students', 'classes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = Classes::orderBy('name', 'asc')->get();

        return view('pages.students.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required',
            'nipd' => 'required|unique:students',
            'class_id' => 'required',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'image_url' => 'nullable',
        ]);

        $studentData = $validated;

        // If image_url is provided, make it accessible from public storage
        if (!empty($studentData['image_url'])) {
            // The path is already stored by the ImageController
            // We just need to make sure it's properly formatted for retrieval
            $studentData['image_url'] = $studentData['image_url'];
        }

        Student::create($studentData);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Student $student)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'Teacher' && $request->has('teacher_classes')) {
            // Check if student belongs to one of teacher's classes
            if (!in_array($student->class_id, $request->teacher_classes)) {
                return redirect()->route('students.index')
                    ->with('error', 'You do not have permission to view this student.');
            }
        }

        $student->load('class');
        return view('pages.students.show', compact('student'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $students = Student::where('name', 'like', "%$search%")->get();
        return view('pages.students.index', compact('students'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Student $student)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'Teacher' && $request->has('teacher_classes')) {
            // Check if student belongs to one of teacher's classes
            if (!in_array($student->class_id, $request->teacher_classes)) {
                return redirect()->route('students.index')
                    ->with('error', 'You do not have permission to edit this student.');
            }

            // Only show classes assigned to this teacher
            $classes = Classes::where('teacher_id', $request->user->id)->get();
        } else {
            // Admin or other roles see all classes
            $classes = Classes::all();
        }

        $student->load('class');
        return view('pages.students.edit', compact('student'), compact('classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required',
            'nipd' => 'required|unique:students,nipd,' . $student->id,
            'class_id' => 'required',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'image_url' => 'nullable',
        ]);

        $updateData = $validated;

        // If a new image is uploaded, update the image_url
        if (!empty($updateData['image_url'])) {
            // The path is already stored by the ImageController
            $updateData['image_url'] = $updateData['image_url'];
        } else {
            // If no new image is uploaded, keep the existing one
            unset($updateData['image_url']);
        }

        $student->update($updateData);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Student $student)
    {
        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'Teacher' && $request->has('teacher_classes')) {
            // Check if student belongs to one of teacher's classes
            if (!in_array($student->class_id, $request->teacher_classes)) {
                return redirect()->route('students.index')
                    ->with('error', 'You do not have permission to delete this student.');
            }
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }

    public function export() {
        return Excel::download(new StudentExport, 'students.xlsx');
    }
}
