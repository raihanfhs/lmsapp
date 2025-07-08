<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\DashboardSheets\UsersByRoleSheet;
use App\Exports\DashboardSheets\UserVerificationStatusSheet;
use App\Exports\DashboardSheets\UserRegistrationTrendsSheet;
use App\Exports\DashboardSheets\CourseStatusSheet;

class DashboardReportExport implements WithMultipleSheets
{
    protected $data;

    // Menerima data yang sudah diformat
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Langsung teruskan data yang sudah siap ke setiap sheet
        $sheets[] = new UsersByRoleSheet($this->data['usersByRole']);
        $sheets[] = new UserVerificationStatusSheet($this->data['userVerificationStatus']);
        $sheets[] = new UserRegistrationTrendsSheet($this->data['userRegistrationTrends']);
        $sheets[] = new CourseStatusSheet($this->data['courseStatusData']);

        return $sheets;
    }
}