<?php namespace App\Exports\DashboardSheets;
use Maatwebsite\Excel\Concerns\FromArray; // Use FromArray for simple arrays
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
class UserVerificationStatusSheet implements FromArray, WithHeadings, WithTitle {
    protected $data; public function __construct(array $data) { $this->data = $data; }
    public function array(): array {
        $rows = [];
        foreach ($this->data['labels'] as $key => $label) {
            $rows[] = [$label, $this->data['data'][$key]];
        }
        return $rows;
    }
    public function headings(): array { return ['Status', 'Count']; }
    public function title(): string { return 'User Verification Status'; }
}