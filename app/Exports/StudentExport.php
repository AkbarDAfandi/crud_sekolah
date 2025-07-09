<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Get all data from student table
        return Student::all();
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Class',
            'NIPD',
            'Gender',
            'Date of Birth',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $student
     *
     * @return array
     */
    public function map($student): array
    {
        return [
            $student->id,
            $student->name,
            $student->class ? $student->class->name : 'N/A',
            $student->nipd,
            $student->gender,
            $student->date_of_birth,
            $student->created_at,
            $student->updated_at,
        ];
    }
}
