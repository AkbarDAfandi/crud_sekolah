<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeachersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Get all data from user table where role is Teacher
        return User::where('role', 'Teacher')->with('subjects')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Subjects',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $teacher
     *
     * @return array
     */
    public function map($teacher): array
    {
        return [
            $teacher->id,
            $teacher->name,
            $teacher->email,
            $teacher->role,
            $teacher->subjects->pluck('name')->implode(', '),
            $teacher->created_at,
            $teacher->updated_at,
        ];
    }
}
