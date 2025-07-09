@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Student</h1>
            <form action="{{ route('students.search') }}" method="GET">
                <div class="form-group mx-sm-3 my-2 d-flex">
                    <input class="form-control mx-2" type="text" name="search" placeholder="Search Students">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>
            <div class="d-grid">
                @if (session('user.role') === 'Admin')
                    <a href="{{ route('students.create') }}"
                        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-solid fa-plus text-white-50"></i> Add New Student
                    </a>
                @endif
                <a href="{{ route('students.export') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-solid fa-file-excel text-white-50"></i> Export Student Data
                </a>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <table class="table table-bordered">
                <tr>
                    <th width="20" class="my-auto">ID</th>
                    <th>Photo</th>
                    <th>Nama</th>
                    <th>NIPD</th>
                    <th>Kelas</th>
                    <th>Gender</th>
                    <th>Tanggal lahir</th>
                    <th width="280px">Action</th>
                </tr>

                @foreach ($students as $student)
                    <tr>
                        <td class="align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">
                            @if ($student->image_url)
                                <img src="{{ asset('storage/' . $student->image_url) }}" alt="{{ $student->name }}"
                                    class="img-thumbnail" style="max-height: 50px;">
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
                        <td>
                            <div class="d-flex">
                                <a class="btn btn-info btn-sm mr-1" href="{{ route('students.show', $student->id) }}">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if(session('user') && session('user')['role'] === 'admin')
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline">
                                        <a class="btn btn-primary btn-sm mr-1" href="{{ route('students.edit', $student->id) }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </tr>
                @endforeach
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
