<?php

namespace App\Http\Controllers;

use App\Models\ClassesTeacher;
use Illuminate\Http\Request;

class ClassesTeacherController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $classesTeacher = ClassesTeacher::create($request->all());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassesTeacher $classesTeacher, Request $request)
    {
        $classesTeacher->update($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassesTeacher $classesTeacher)
    {
        $classesTeacher->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassesTeacher $classesTeacher)
    {
        $classesTeacher->delete();
    }
}
