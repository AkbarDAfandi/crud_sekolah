@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Student Attendance</h1>
            @if (isset($subject) && isset($class))
                <div class="d-flex">
                    <h5 class="mr-3">
                        <span class="badge badge-primary">Class: {{ $class->name }}</span>
                        <span class="badge badge-info">Subject: {{ $subject->name }}</span>
                    </h5>
                    <a href="{{ route('attendance.classPicker') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left text-white-50"></i>Back to Classes
                    </a>
                </div>
            @endif
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('fail'))
            <div class="alert alert-warning">
                {{ session('fail') }}
            </div>
        @endif

        <!-- Content Row -->
        <div class="row">
            @if (count($students) > 0)
                <div class="col-12">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>NIPD</th>
                                <th>Class</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Status</th>
                                @if (session('user.role') === 'Teacher')
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php
                                    $studentAttendance = $attendanceHistory->where('student_id', $student->id)->first();
                                @endphp
                                <tr
                                    class="{{ $studentAttendance ? 'table-' . ($studentAttendance->status === 'present' ? 'success' : ($studentAttendance->status === 'absent' ? 'danger' : 'warning')) : '' }}">
                                    <td class="align-middle">
                                        @if ($student->image_url)
                                            <img src="{{ asset('storage/' . $student->image_url) }}"
                                                alt="{{ $student->name }}" class="img-thumbnail" style="max-height: 50px;">
                                        @else
                                            <img src="{{ asset('img/default-profile.png') }}" alt="Default profile"
                                                class="img-thumbnail" style="max-height: 50px;">
                                        @endif
                                    </td>
                                    <td class="align-middle">{{ $student->name }}</td>
                                    <td class="align-middle">{{ $student->nipd }}</td>
                                    <td class="align-middle">{{ $student->class->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $student->gender }}</td>
                                    <td class="align-middle">{{ $student->date_of_birth }}</td>
                                    <td class="align-middle">
                                        @if ($studentAttendance)
                                            <span
                                                class="badge bg-{{ $studentAttendance->status === 'present' ? 'success' : ($studentAttendance->status === 'absent' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($studentAttendance->status) }}
                                                @if ($studentAttendance->note)
                                                    <i class="fas fa-info-circle"
                                                        title="{{ $studentAttendance->note }}"></i>
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white">Not Marked</span>
                                        @endif
                                    </td>
                                    @if (session('user.role') === 'Teacher')
                                        <td class="align-middle">
                                            @if (!$studentAttendance)
                                                <form action="{{ route('attendance.attendance', $student->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="status" value="present">
                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                    <button type="submit" class="btn btn-success btn-sm mr-1"
                                                        title="Mark as Present">
                                                        <i class="fas fa-check"></i> Present
                                                    </button>
                                                </form>
                                                <form action="{{ route('attendance.attendance', $student->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="status" value="absent">
                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm mr-1"
                                                        title="Mark as Absent">
                                                        <i class="fas fa-times"></i> Absent
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#otherAttendanceModal{{ $student->id }}">
                                                    <i class="fas fa-edit"></i> Other
                                                </button>

                                                <!-- Other Status Modal -->
                                                <div class="modal fade" id="otherAttendanceModal{{ $student->id }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="otherAttendanceModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="otherAttendanceModalLabel">
                                                                    Other Attendance
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form
                                                                action="{{ route('attendance.attendance', $student->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('POST')
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label for="status">Status</label>
                                                                        <select class="form-control" id="status"
                                                                            name="status" required>
                                                                            <option value="sick">Sick</option>
                                                                            <option value="permission">Permission
                                                                            </option>
                                                                            <option value="late">Late</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="note">Note (Optional)</label>
                                                                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Save
                                                                        changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">No students found for attendance</div>
            @endif
        </div>
    </div>
@endsection
