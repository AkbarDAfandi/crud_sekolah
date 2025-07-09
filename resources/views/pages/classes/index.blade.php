@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Class</h1>
            @if(session('user.role') === 'Admin')
            <a href="{{ route('classes.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-solid fa-plus text-white-50"></i> Add New Class
                </a>
            @endif
        </div>

        <!-- Content Row -->
        <div class="row">
            <table class="table table-bordered">
                <tr>
                    <th width="20" class="my-auto">ID</th>
                    <th>Name</th>
                    <th>Teacher</th>
                    @if(session('user.role') === 'Admin')
                        <th width="280px">Action</th>
                    @endif
                </tr>

                @foreach ($classes as $class)
                    <tr>
                        <td class="align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $class->name }}</td>
                        <td class="align-middle">{{ $class->teacher->name }}</td>
                        @if(session('user.role') === 'Admin')
                            <td>
                                <form action="{{ route('classes.destroy', $class->id) }}" method="POST">
                                    <a class="btn btn-primary btn-sm" href="{{ route('classes.edit', $class->id) }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this class?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>

            {{-- {!! $teachers->links() !!} --}}

        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
