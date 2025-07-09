@extends('layouts.layout')


@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add new Student</h1>
    </div>

    <!-- Content Row -->

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="w-100" action="{{ route('students.store') }}" id="student-form" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="image_url" id="image_path">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" class="form-control" placeholder="Name">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>NIPD:</strong>
                    <input type="text" name="nipd" class="form-control" placeholder="NIPD">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Class:</strong>
                    <select name="class_id" class="form-control">
                        <option value="">-- Select a Class --</option>
                        @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Gender:</strong>
                    <select name="gender" class="form-control">
                        <option value="">-- Select a Gender --</option>
                        <option value="L">L</option>
                        <option value="P">P</option>
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Date of Birth:</strong>
                    <input type="date" name="date_of_birth" class="form-control" placeholder="Date of Birth">
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 mb-4">
            <div class="form-group">
                <strong>Student Photo:</strong>
                <div id="image-upload" class="dropzone mt-2">
                    <div class="dz-message" data-dz-message>
                        <span>Drop student photo here or click to upload</span>
                    </div>
                </div>
                <small class="form-text text-muted">Upload a photo of the student (max 2MB, image files only)</small>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-4">
            <button type="button" id="submit-all" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

@endsection