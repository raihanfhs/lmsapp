<?php

namespace App\Exports\DashboardSheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class UsersByRoleSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $usersByRole;

    public function __construct($usersByRole)
    {
        $this->usersByRole = $usersByRole;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Convert objects to arrays for export
        return collect($this->usersByRole)->map(function($item) {
            return (array) $item;
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Role',
            'Count',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Users by Role';
    }
}