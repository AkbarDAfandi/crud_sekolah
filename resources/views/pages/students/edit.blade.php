@extends('layouts.layout')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Student</h1>
        </div>

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

        <form action="{{ route('students.update', $student) }}" id="student-form" method="POST" enctype="multipart/form-data"> @csrf
            @method('PUT')
            <input type="hidden" name="image_url" id="image_path" value="{{ $student->image_url }}">

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" value="{{ $student->name }}" class="form-control"
                            placeholder="Name">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>NIPD:</strong>
                        <input type="text" name="nipd" value="{{ $student->nipd }}" class="form-control"
                            placeholder="NIPD">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Class:</strong>
                        <select name="class_id" class="form-control">
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    @if (isset($class) && $class->id == $student->class_id) selected @endif>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Gender:</strong>
                        <select name="gender" class="form-control">
                            <option value="L" {{ $student->gender == 'L' ? 'selected' : '' }}>L</option>
                            <option value="P" {{ $student->gender == 'P' ? 'selected' : '' }}>P</option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Date of Birth:</strong>
                        <input type="date" name="date_of_birth" value="{{ $student->date_of_birth }}"
                            class="form-control" placeholder="Date of Birth">
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
                        @if($student->image_url)
                            <div class="mt-2">
                                <strong>Current Image:</strong>
                                <img src="{{ asset('storage/' . $student->image_url) }}" alt="{{ $student->name }}" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="button" id="submit-all" class="btn btn-primary w-100">Save Changes</button>
                </div>
        </form>
    </div>
@endsection
