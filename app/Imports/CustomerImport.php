<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importMode;
    private $importedCount = 0;
    private $skippedCount = 0;
    private $errorCount = 0;
    private $errors = [];

    public function __construct($importMode = 'create')
    {
        $this->importMode = $importMode;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Skip empty rows
            if (empty($row['name']) || empty($row['email'])) {
                $this->skippedCount++;
                return null;
            }

            // Check if customer already exists (for update/replace modes)
            $existingCustomer = null;
            if (in_array($this->importMode, ['update', 'replace'])) {
                $existingCustomer = Customer::where('email', $row['email'])->first();
            }

            // Handle different import modes
            switch ($this->importMode) {
                case 'create':
                    if ($existingCustomer) {
                        $this->skippedCount++;
                        $this->errors[] = "Row " . ($this->importedCount + $this->skippedCount + $this->errorCount) . ": Customer already exists with email '{$row['email']}'";
                        return null;
                    }
                    return $this->createCustomer($row);

                case 'update':
                    if ($existingCustomer) {
                        $this->updateCustomer($existingCustomer, $row);
                        $this->importedCount++;
                        return null; // Don't create new model, just update existing
                    } else {
                        return $this->createCustomer($row);
                    }

                case 'replace':
                    if ($existingCustomer) {
                        $existingCustomer->delete();
                    }
                    return $this->createCustomer($row);

                default:
                    return $this->createCustomer($row);
            }

        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Row " . ($this->importedCount + $this->skippedCount + $this->errorCount) . ": " . $e->getMessage();
            Log::error('Customer import error: ' . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    /**
     * Create a new customer
     */
    private function createCustomer($row)
    {
        $customer = new Customer([
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'country' => $row['country'] ?? null,
            'age' => isset($row['age']) ? (int) $row['age'] : null,
            'loyalty_points' => isset($row['loyalty_points']) ? (int) $row['loyalty_points'] : 0,
            'total_spent' => isset($row['total_spent']) ? (float) $row['total_spent'] : 0.00,
            'status' => $row['status'] ?? 'new',
            'segment' => $row['segment'] ?? 'new',
            'notes' => $row['notes'] ?? null,
            'date_of_birth' => isset($row['date_of_birth']) ? $this->parseDate($row['date_of_birth']) : null,
            'gender' => $row['gender'] ?? null,
            'emergency_contact' => $row['emergency_contact'] ?? null,
            'medical_conditions' => $row['medical_conditions'] ?? null,
            'allergies' => $row['allergies'] ?? null,
            'insurance_provider' => $row['insurance_provider'] ?? null,
            'insurance_number' => $row['insurance_number'] ?? null
        ]);

        $customer->save();
        $this->importedCount++;
        return $customer;
    }

    /**
     * Update existing customer
     */
    private function updateCustomer($customer, $row)
    {
        $customer->update([
            'name' => $row['name'],
            'phone' => $row['phone'] ?? $customer->phone,
            'address' => $row['address'] ?? $customer->address,
            'city' => $row['city'] ?? $customer->city,
            'country' => $row['country'] ?? $customer->country,
            'age' => isset($row['age']) ? (int) $row['age'] : $customer->age,
            'loyalty_points' => isset($row['loyalty_points']) ? (int) $row['loyalty_points'] : $customer->loyalty_points,
            'total_spent' => isset($row['total_spent']) ? (float) $row['total_spent'] : $customer->total_spent,
            'status' => $row['status'] ?? $customer->status,
            'segment' => $row['segment'] ?? $customer->segment,
            'notes' => $row['notes'] ?? $customer->notes,
            'date_of_birth' => isset($row['date_of_birth']) ? $this->parseDate($row['date_of_birth']) : $customer->date_of_birth,
            'gender' => $row['gender'] ?? $customer->gender,
            'emergency_contact' => $row['emergency_contact'] ?? $customer->emergency_contact,
            'medical_conditions' => $row['medical_conditions'] ?? $customer->medical_conditions,
            'allergies' => $row['allergies'] ?? $customer->allergies,
            'insurance_provider' => $row['insurance_provider'] ?? $customer->insurance_provider,
            'insurance_number' => $row['insurance_number'] ?? $customer->insurance_number
        ]);
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y-m-d H:i:s'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }

            // If all formats fail, try Carbon's parse
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: {$dateString}");
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0|max:150',
            'loyalty_points' => 'nullable|integer|min:0',
            'total_spent' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:new,active,inactive,premium',
            'segment' => 'nullable|in:new,regular,loyal,vip',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date'
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Customer name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'age.integer' => 'Age must be a number',
            'age.min' => 'Age must be at least 0',
            'age.max' => 'Age must be at most 150',
            'loyalty_points.integer' => 'Loyalty points must be a number',
            'loyalty_points.min' => 'Loyalty points must be at least 0',
            'total_spent.numeric' => 'Total spent must be a number',
            'total_spent.min' => 'Total spent must be at least 0',
            'status.in' => 'Status must be one of: new, active, inactive, premium',
            'segment.in' => 'Segment must be one of: new, regular, loyal, vip',
            'gender.in' => 'Gender must be one of: male, female, other',
            'date_of_birth.date' => 'Date of birth must be a valid date'
        ];
    }

    /**
     * Batch size for inserts
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get imported count
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }

    /**
     * Get skipped count
     */
    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    /**
     * Get error count
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
