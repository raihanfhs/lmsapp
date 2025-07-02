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

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // Add sheets for each chart data
        $sheets[] = new UsersByRoleSheet($this->data['usersByRole']);
        $sheets[] = new UserVerificationStatusSheet($this->data['userVerificationStatus']);
        $sheets[] = new UserRegistrationTrendsSheet($this->data['userRegistrationTrends']);
        $sheets[] = new CourseStatusSheet($this->data['courseStatusData']);

        return $sheets;
    }
}