@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">School Subject</h1>
            @if(session('user.role') === 'Admin')
            <a href="{{ route('subjects.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-solid fa-plus text-white-50"></i> Add New Subject
                </a>
            @endif
        </div>

        <!-- Content Row -->
        <div class="row">
            <table class="table table-bordered">
                <tr>
                    <th width="20" class="my-auto">ID</th>
                    <th>Name</th>
                    <th width="280px">Action</th>
                </tr>

                @foreach ($subjects as $subject)
                    <tr>
                        <td class="align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $subject->name }}</td>
                        <td>
                            @if(session('user.role') === 'Admin')
                                <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
                                    <a class="btn btn-primary btn-sm mr-1" href="{{ route('subjects.edit', $subject->id) }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subject?')">
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
