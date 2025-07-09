<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected array $teacherClassIds;

    public function __construct(Carbon $startDate, Carbon $endDate, array $teacherClassIds)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->teacherClassIds = $teacherClassIds;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get necessary values from constructor
        $query = Attendance::with(['student', 'teacher', 'subject', 'class']);

        // Get only selected date range
        $query->whereBetween('date', [$this->startDate, $this->endDate]);

        // Get only selected teacher class ids
        if (!empty($this->teacherClassIds)) {
            $query->whereIn('class_id', $this->teacherClassIds);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Teacher Name',
            'Subject Name',
            'Class Name',
            'Date',
            'Status',
            'Note',
            'Created At',
            'Updated At'
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->id,
            $attendance->student->name,
            $attendance->teacher->name,
            $attendance->subject->name,
            $attendance->class->name,
            Carbon::parse($attendance->date)->format('Y-m-d'),
            $attendance->status,
            $attendance->note,
            $attendance->created_at,
            $attendance->updated_at,
        ];
    }
}
