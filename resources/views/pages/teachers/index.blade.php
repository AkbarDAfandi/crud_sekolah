@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Teachers</h1>
            <form action="{{ route('teachers.search') }}" method="GET">
                <div class="form-group mx-sm-3 my-2 d-flex">
                    <input class="form-control mx-2" type="text" name="search" placeholder="Search Teachers">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>
            <div class="d-grid">
            <a href="{{ route('teachers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-solid fa-plus text-white-50"></i> Add New Teacher
                </a>
            <a href="{{ route('teachers.export') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-solid fa-file-excel text-white-50"></i> Export Teacher Data
            </a>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <table class="table table-bordered">
                <tr>
                    <th width="20" class="my-auto">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Role</th>

                    <th width="280px">Action</th>
                </tr>

                @foreach ($teachers as $teacher)
                    <tr>
                        <td class="align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $teacher->name }}</td>
                        <td class="align-middle">{{ $teacher->email }}</td>
                        <td class="align-middle">
                            @forelse ($teacher->subjects as $subject)
                                <span class="badge badge-primary">{{ $subject->name }}</span>
                            @empty
                                <span class="badge badge-secondary">Not Assigned</span>
                            @endforelse
                        </td>
                        <td class="align-middle">{{ $teacher->role }}</td>
                        <td>
                            @if(session('user.role') === 'Admin')
                                <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                    <a class="btn btn-primary btn-sm mr-1" href="{{ route('teachers.edit', $teacher->id) }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this teacher?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">View only</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            {{-- {!! $teachers->links() !!} --}}

        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
