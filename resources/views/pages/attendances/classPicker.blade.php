@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @foreach ($classes as $class)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col">
                                    <div class="h5 font-weight-bold text-primary text-uppercase mb-1">
                                        {{ $class->name }}
                                    </div>
                                    @if($class->teacher && $class->teacher->subjects)
                                        <div class="mt-2">
                                            <div class="text-xs font-weight-bold text-gray-600 mb-1">Subjects:</div>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($class->teacher->subjects as $subject)
                                                <button onclick="window.location.href='{{ route('attendance.index', ['class' => $class->id, 'subject' => $subject->id]) }}'"
                                                    class="btn btn-primary btn-sm mx-1">
                                                {{ $subject->name }}
                                            </button>
                                            @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-xs text-muted mt-2">No subjects assigned to teacher</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
