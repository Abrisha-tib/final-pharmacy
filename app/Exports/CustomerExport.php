<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    private $filters;
    private $templateData;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Set template data for import template
     */
    public function setTemplateData($templateData)
    {
        $this->templateData = $templateData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->templateData) {
            return collect($this->templateData);
        }

        $query = Customer::query();

        // Apply filters
        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['segment']) && $this->filters['segment']) {
            $query->where('segment', $this->filters['segment']);
        }

        if (isset($this->filters['city']) && $this->filters['city']) {
            $query->where('city', 'like', '%' . $this->filters['city'] . '%');
        }

        if (isset($this->filters['date_from']) && $this->filters['date_from']) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to']) && $this->filters['date_to']) {
            $query->where('created_at', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['min_spent']) && $this->filters['min_spent']) {
            $query->where('total_spent', '>=', $this->filters['min_spent']);
        }

        if (isset($this->filters['max_spent']) && $this->filters['max_spent']) {
            $query->where('total_spent', '<=', $this->filters['max_spent']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        if ($this->templateData) {
            return $this->templateData[0] ?? [];
        }

        return [
            'Name*',
            'Email*',
            'Phone',
            'Address',
            'City',
            'Country',
            'Age',
            'Loyalty Points',
            'Total Spent',
            'Status',
            'Segment',
            'Notes',
            'Date of Birth (YYYY-MM-DD)',
            'Gender',
            'Emergency Contact',
            'Medical Conditions',
            'Allergies',
            'Insurance Provider',
            'Insurance Number',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     */
    public function map($customer): array
    {
        if ($this->templateData && is_array($customer)) {
            return $customer;
        }

        return [
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->address,
            $customer->city,
            $customer->country,
            $customer->age,
            $customer->loyalty_points,
            $customer->total_spent,
            $customer->status,
            $customer->segment,
            $customer->notes,
            $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '',
            $customer->gender,
            $customer->emergency_contact,
            $customer->medical_conditions,
            $customer->allergies,
            $customer->insurance_provider,
            $customer->insurance_number,
            $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : '',
            $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i:s') : ''
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Name
            'B' => 25, // Email
            'C' => 15, // Phone
            'D' => 30, // Address
            'E' => 15, // City
            'F' => 15, // Country
            'G' => 8,  // Age
            'H' => 15, // Loyalty Points
            'I' => 15, // Total Spent
            'J' => 12, // Status
            'K' => 12, // Segment
            'L' => 30, // Notes
            'M' => 15, // Date of Birth
            'N' => 10, // Gender
            'O' => 20, // Emergency Contact
            'P' => 25, // Medical Conditions
            'Q' => 25, // Allergies
            'R' => 20, // Insurance Provider
            'S' => 20, // Insurance Number
            'T' => 20, // Created At
            'U' => 20, // Updated At
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add borders to all cells
                $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Auto-fit columns
                foreach (range('A', 'U') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Add data validation for status and segment columns
                if (!$this->templateData) {
                    $lastRow = $sheet->getHighestRow();
                    
                    // Status validation (J column)
                    $statusValidation = $sheet->getDataValidation('J2:J' . $lastRow);
                    $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $statusValidation->setFormula1('"new,active,inactive,premium"');
                    $statusValidation->setShowDropDown(true);
                    $statusValidation->setShowErrorMessage(true);
                    $statusValidation->setErrorTitle('Invalid Status');
                    $statusValidation->setError('Please select a valid status: new, active, inactive, premium');
                    
                    // Segment validation (K column)
                    $segmentValidation = $sheet->getDataValidation('K2:K' . $lastRow);
                    $segmentValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $segmentValidation->setFormula1('"new,regular,loyal,vip"');
                    $segmentValidation->setShowDropDown(true);
                    $segmentValidation->setShowErrorMessage(true);
                    $segmentValidation->setErrorTitle('Invalid Segment');
                    $segmentValidation->setError('Please select a valid segment: new, regular, loyal, vip');
                    
                    // Gender validation (N column)
                    $genderValidation = $sheet->getDataValidation('N2:N' . $lastRow);
                    $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $genderValidation->setFormula1('"male,female,other"');
                    $genderValidation->setShowDropDown(true);
                    $genderValidation->setShowErrorMessage(true);
                    $genderValidation->setErrorTitle('Invalid Gender');
                    $genderValidation->setError('Please select a valid gender: male, female, other');
                }
            }
        ];
    }
}
