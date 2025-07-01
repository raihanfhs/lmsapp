<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // <--- Add this

class UsersExport implements FromCollection, WithHeadings // <--- Implement WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Fetch all users with their roles (if roles are desired in the report)
        // Select specific columns you want in your Excel file
        return User::select('id', 'name', 'email', 'created_at')
                    ->with('roles') // Eager load roles
                    ->get()
                    ->map(function ($user) {
                        return [
                            'ID' => $user->id,
                            'Name' => $user->name,
                            'Email' => $user->email,
                            'Roles' => $user->roles->pluck('name')->implode(', '), // List all roles
                            'Registered At' => $user->created_at->format('Y-m-d H:i:s'), // Format date
                        ];
                    });
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
            'Roles',
            'Registered At',
        ];
    }
}