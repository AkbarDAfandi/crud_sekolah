<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Classes;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classes::all();
        $subjects = Subject::all();


        return view('pages.subjects.index', compact('classes', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::all();

        return view('pages.subjects.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        Subject::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('subjects.index')
            ->with('success', 'Subject created succesfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        $teachers = User::all();

        return view('pages.subjects.edit', compact('subject', 'teachers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        $teachers = User::all();

        return view('pages.subjects.edit', compact('subject', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required',
            'teacher_id' => 'required'
        ]);

        $updateData = [
            'name' => $validated['name'],
            'teacher_id' => $validated['teahcer_id']
        ];

        return redirect('subject.index')
        ->with('success', 'Subject updated succesfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success','Subject deleted successfully');
    }
}
