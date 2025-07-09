@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Student Details</h1>
            <a href="{{ route('students.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left text-white-50"></i> Back to List
            </a>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        @if($student->image_url)
                            <img src="{{ asset('storage/' . $student->image_url) }}" alt="{{ $student->name }}" class="img-fluid rounded mb-3" style="max-height: 250px;">
                        @else
                            <div class="bg-light p-5 rounded mb-3 d-flex align-items-center justify-content-center" style="height: 250px;">
                                <i class="fas fa-user fa-5x text-gray-300"></i>
                            </div>
                        @endif
                        <h4 class="font-weight-bold">{{ $student->name }}</h4>
                        <p class="text-muted">NIPD: {{ $student->nipd }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <th width="30%">Name</th>
                                        <td>{{ $student->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>NIPD</th>
                                        <td>{{ $student->nipd }}</td>
                                    </tr>
                                    <tr>
                                        <th>Class</th>
                                        <td>{{ $student->class->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ $student->gender }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{ $student->date_of_birth }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection