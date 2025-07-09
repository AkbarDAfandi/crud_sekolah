<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;
use App\Exports\AttendanceExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{

    public function classPicker(Request $request)
    {
        // Get teacher's class IDs with teacher and subjects
        $classes = \App\Models\Classes::with(['teacher', 'teacher.subjects'])
            ->where('teacher_id', $request->user->id)
            ->get();

        // Reject access if a teacher is not assigned to a class
        if ($classes->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('fail', 'You are not assigned to any classes.');
        }

        // Insert teacher's class IDs into an array
        $teacherClassIds = $classes->pluck('id')->toArray();

        // Only show students in classes assigned to this teacher
        $students = Student::whereIn('class_id', $teacherClassIds)
            ->with('class')
            ->get();

        return view('pages.attendances.classPicker', compact('classes', 'students'));
    }

    public function index(Request $request)
    {
        // Get today's date
        $today = now()->toDateString();

        // Get class and subject IDs from query parameters
        $classId = $request->query('class');
        $subjectId = $request->query('subject');

        // Get subject information
        $subject = Subject::findOrFail($subjectId);

        // Get class information
        $class = Classes::findOrFail($classId);

        // Check if user is a teacher with restricted access
        if ($request->has('user') && $request->user->role === 'Teacher') {
            // Verify the teacher is assigned to this class
            if ($class->teacher_id !== $request->user->id) {
                return redirect()->route('dashboard')
                    ->with('fail', 'You are not authorized to view this class.');
            }

            // Get students in this class who haven't been marked for this subject today
            $students = Student::where('class_id', $classId)
                ->whereDoesntHave('attendances', function ($query) use ($today, $subjectId) {
                    $query->whereDate('date', $today)
                        ->where('subject_id', $subjectId);
                })
                ->with('class')
                ->get();

            // Get attendance history for this class and subject
            $attendanceHistory = Attendance::where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->whereDate('date', $today)
                ->with(['student', 'subject'])
                ->get();

            return view('pages.attendances.attendance', compact('students', 'class', 'subject', 'attendanceHistory'));
            $classes = Classes::all();
        }

        // Get all necessary data for attendance view
        $attendances = Attendance::all();
        $subjects = Subject::all();
        $teachers = User::all();

        return view('pages.attendances.attendance', compact('attendances', 'students', 'classes', 'subjects', 'teachers'));
    }


    public function attendance(Request $request, Student $student)
    {
        // Get today's date & subject id from request
        $today = now()->toDateString();
        $subjectId = $request->input('subject_id');

        // Validate required fields
        if (!$subjectId) {
            return redirect()->back()->with('fail', 'Subject ID is required.');
        }

        // Load student's class and teacher
        $student->load(['class.teacher']);

        // Check if student is assigned to a class
        if (!$student->class) {
            return redirect()->back()->with('error', 'Student is not assigned to any class.');
        }

        // Verify the teacher is assigned to this class
        $teacher = $student->class->teacher;
        if (!$teacher || $teacher->id != $request->user->id) {
            return redirect()->back()->with('error', 'You are not authorized to record attendance for this class.');
        }

        // Verify the subject is assigned to this teacher
        $subject = $teacher->subjects()->where('subjects.id', $subjectId)->first();
        if (!$subject) {
            return redirect()->back()->with('error', 'You are not authorized to record attendance for this subject.');
        }

        // Check if attendance already exists for today for this student and subject
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', $today)
            ->first();

        // Proccess POST request
        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:present,absent,sick,permission,late',
                'note' => 'nullable|string|max:255',
            ]);

            try {
                if ($existingAttendance) {
                    // Update existing attendance
                    $existingAttendance->update([
                        'status' => $request->status,
                        'note' => $request->note,
                        'teacher_id' => $teacher->id,
                    ]);
                } else {
                    // Create new attendance record
                    Attendance::create([
                        'student_id' => $student->id,
                        'status' => $request->status,
                        'note' => $request->note,
                        'subject_id' => $subject->id,
                        'class_id' => $student->class_id,
                        'date' => now(),
                        'teacher_id' => $teacher->id,
                    ]);
                }

                // Return a Success message
                return redirect()->back()->with('success', 'Attendance recorded successfully!');
            } catch (\Exception $e) {
                // Return an Error message
                return redirect()->back()->with('error', 'An error occurred while saving attendance: ' . $e->getMessage());
            }
        }

        // For GET request, get today's attendance if it exists
        $attendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', today())
            ->first();

        // Return the attendance view
        return view('pages.attendances.attendance', [
            'student' => $student,
            'attendance' => $attendance,
            'subject' => $subject,
            'class' => $student->class,
            'attendanceHistory' => collect(),
            'students' => collect([$student])
        ]);
    }

    public function history(Request $request)
    {
        // Get all necessary data for attendance history
        $query = Attendance::with(['student', 'teacher', 'subject', 'class']);

        // Check if user is a teacher with restricted access
        if ($request->user() && $request->user()->role === 'Teacher') {
            // Get teacher's class IDs
            $teacherClassIds = \App\Models\Classes::where('teacher_id', $request->user()->id)
                ->pluck('id')
                ->toArray();

            // Check if teacher is assigned to a classes
            if (empty($teacherClassIds)) {
                return redirect()->route('dashboard')
                    ->with('fail', 'You are not assigned to any classes.');
            }

            // Add filter for the query to only include students in the teacher's classes
            $query->whereHas('student', function ($query) use ($teacherClassIds) {
                $query->whereIn('class_id', $teacherClassIds);
            });
        }

        // Date range filtering
        $dateRange = $request->input('date_range');
        $dayDate = $request->input('day_date');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Filter dateRange with switch case
        if ($dateRange) {
            switch ($dateRange) {
                case 'day':
                    $query->whereDate('date', $dayDate);
                    break;
                case 'month':
                    $query->whereMonth('date', date('m', strtotime($dayDate)))
                        ->whereYear('date', date('Y', strtotime($dayDate)));
                    break;
                case 'year':
                    $query->whereYear('date', date('Y', strtotime($dayDate)));
                    break;
                case 'range':
                    $query->whereBetween('date', [$startDate, $endDate]);
                    break;
            }
        }

        // Format data with pagination
        $attendances = $query->paginate(15)->appends($request->all());

        // Return view with data
        return view('pages.attendances.history', compact('attendances'));
    }

    public function export(Request $request)
    {
        // Get dataRange from request
        $dateRange = $request->input('date_range');

        // Validate dataRange request
        $request->validate([
            'date_range' => 'required|in:day,month,year,range',
            'day_date' => 'required_if:date_range,day|nullable|date',
            'start_date' => 'required_if:date_range,month,year,range|nullable|date',
            'end_date' => 'required_if:date_range,range|nullable|date|after_or_equal:start_date',
        ]);

        // Initialize necessary variable
        $dateRange = $request->input('date_range');
        $startDate = null;
        $endDate = null;
        $filename = "attendance-";

        // Filter dataRange with switch case
        switch ($dateRange) {
            case 'day':
                $date = Carbon::parse($request->input('day_date'));
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                $filename .= "daily-" . $date->format('Y-m-d');
                break;
            case 'month':
                $date = Carbon::parse($request->input('start_date'));
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
                $filename .= "monthly-" . $date->format('Y-m');
                break;
            case 'year':
                $date = Carbon::parse($request->input('start_date'));
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
                $filename .= "yearly-" . $date->format('Y');
                break;
            case 'range':
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                $filename .= "range-" . $startDate->format('Y-m-d') . "-" . $endDate->format('Y-m-d');
                break;
        }

        // Get user data
        $user = $request->user();
        $teacherClassIds = [];

        // Check if user is a teacher
        if ($user && $user->role === 'Teacher') {
            $teacherClassIds = \App\Models\Classes::where('teacher_id', $user->id)
                ->pluck('id')
                ->toArray();

            // Check if teacher is assigned to a class to access export
            if (empty($teacherClassIds)) {
                return redirect()->back()->with('fail', 'You are not assigned to any classes to export data.');
            }
        }

        // Return Excel file
        return Excel::download(new AttendanceExport($startDate, $endDate, $teacherClassIds), $filename . '.xlsx');
    }
}
