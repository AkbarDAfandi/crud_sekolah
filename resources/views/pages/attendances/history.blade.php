@extends('layouts.layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Attendance History</h1>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Attendance History</h5>
                <form method="GET" action="{{ route('attendance.export') }}" class="mb-0" id="exportForm">
                    @csrf
                    <input type="hidden" name="date_range" value="{{ request('date_range', 'day') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-export"></i> Export to Excel
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('attendance.history') }}" id="filterForm">
                    <div class="form-group">
                        <label>Date Range</label>
                        <select class="form-control" name="date_range" id="date_range">
                            <option value="" {{ !request()->has('date_range') ? 'selected' : '' }}>All Dates</option>
                            <option value="day" {{ request('date_range') == 'day' ? 'selected' : '' }}>Daily</option>
                            <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>Yearly</option>
                            <option value="range" {{ request('date_range') == 'range' ? 'selected' : '' }}>Custom Range
                            </option>
                        </select>
                    </div>

                    <div class="form-group" id="day_group">
                        <label>Select Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div class="form-group d-none" id="range_group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Note</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($attendance->student->image_url)
                                            <img src="{{ asset('storage/' . $attendance->student->image_url) }}"
                                                alt="{{ $attendance->student->name }}" class="img-thumbnail mr-2"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('img/default-profile.png') }}" alt="Default profile"
                                                class="img-thumbnail mr-2"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div>{{ $attendance->student->name }}</div>
                                            <small class="text-muted">NIPD: {{ $attendance->student->nipd }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $attendance->student->class->name ?? '-' }}</td>
                                <td>{{ $attendance->subject->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'sick' => 'warning',
                                            'permission' => 'info',
                                            'late' => 'secondary',
                                        ];
                                        $color = $statusColors[$attendance->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->date->format('d M Y') }}</td>
                                <td>{{ $attendance->note ?? '-' }}</td>
                                <td>{{ $attendance->teacher->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
    <!-- /.container-fluid -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateRange = document.getElementById('date_range');
            const dayGroup = document.getElementById('day_group');
            const rangeGroup = document.getElementById('range_group');
            const exportForm = document.getElementById('exportForm');
            const filterForm = document.getElementById('filterForm');

            function updateExportForm() {
                const dateRangeValue = dateRange.value;
                const exportDateRange = exportForm.querySelector('input[name="date_range"]');
                const exportStartDate = exportForm.querySelector('input[name="start_date"]');
                const exportEndDate = exportForm.querySelector('input[name="end_date"]');

                // Get values from the filter form
                const filterStartDate = filterForm.querySelector('input[name="start_date"]');
                const filterEndDate = filterForm.querySelector('input[name="end_date"]');

                // Update export form values
                exportDateRange.value = dateRangeValue;
                exportStartDate.value = filterStartDate ? filterStartDate.value : '';

                if (dateRangeValue === 'range' && filterEndDate) {
                    exportEndDate.value = filterEndDate.value;
                } else {
                    exportEndDate.value = '';
                }
            }

            function updateFormFields() {
                const value = dateRange.value;

                // Hide all groups first
                dayGroup.classList.add('d-none');
                rangeGroup.classList.add('d-none');

                // Show the appropriate group
                if (value === 'day' || value === 'month' || value === 'year') {
                    dayGroup.classList.remove('d-none');
                } else if (value === 'range') {
                    rangeGroup.classList.remove('d-none');
                }

                updateExportForm();
            }

            // Update form fields when page loads
            updateFormFields();

            // Update form fields when date range changes
            dateRange.addEventListener('change', updateFormFields);

            // Update export form when filter form is submitted
            filterForm.addEventListener('change', updateExportForm);
            filterForm.addEventListener('input', updateExportForm);

            
        });

        if (window.history.replaceState && window.location.search) {
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    </script>
@endsection
