<?php

namespace App\Imports;

use App\Models\Medicine;
use App\Models\Category;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MedicineImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError, SkipsOnFailure
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
            if (empty($row['name']) || empty($row['batch_number'])) {
                $this->skippedCount++;
                return null;
            }

            // Validate category exists
            $categoryId = $this->getCategoryId($row);
            if (!$categoryId) {
                $this->errorCount++;
                $this->errors[] = "Row " . ($this->importedCount + $this->skippedCount + $this->errorCount) . ": Category not found for '{$row['category_id']}'";
                return null;
            }

            // Check if medicine already exists (for update/replace modes)
            $existingMedicine = null;
            if (in_array($this->importMode, ['update', 'replace'])) {
                $existingMedicine = Medicine::where('batch_number', $row['batch_number'])
                    ->where('name', $row['name'])
                    ->first();
            }

            // Handle different import modes
            switch ($this->importMode) {
                case 'create':
                    if ($existingMedicine) {
                        $this->skippedCount++;
                        $this->errors[] = "Row " . ($this->importedCount + $this->skippedCount + $this->errorCount) . ": Medicine already exists with batch number '{$row['batch_number']}'";
                        return null;
                    }
                    return $this->createMedicine($row, $categoryId);

                case 'update':
                    if ($existingMedicine) {
                        $this->updateMedicine($existingMedicine, $row, $categoryId);
                        $this->importedCount++;
                        return null; // Don't create new model, just update existing
                    } else {
                        return $this->createMedicine($row, $categoryId);
                    }

                case 'replace':
                    if ($existingMedicine) {
                        $existingMedicine->delete();
                    }
                    return $this->createMedicine($row, $categoryId);

                default:
                    return $this->createMedicine($row, $categoryId);
            }

        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Row " . ($this->importedCount + $this->skippedCount + $this->errorCount) . ": " . $e->getMessage();
            Log::error('Medicine import error: ' . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    /**
     * Create a new medicine
     */
    private function createMedicine($row, $categoryId)
    {
        $medicine = new Medicine([
            'name' => $row['name'],
            'generic_name' => $row['generic_name'] ?? null,
            'manufacturer' => $row['manufacturer'] ?? null,
            'category_id' => $categoryId,
            'strength' => $row['strength'],
            'form' => $row['form'],
            'unit' => $row['unit'] ?? null,
            'barcode' => $row['barcode'] ?? null,
            'batch_number' => $row['batch_number'],
            'stock_quantity' => (int) $row['stock_quantity'],
            'reorder_level' => isset($row['reorder_level']) ? (int) $row['reorder_level'] : null,
            'selling_price' => (float) $row['selling_price'],
            'cost_price' => (float) $row['cost_price'],
            'prescription_required' => $this->parseBoolean($row['prescription_required'] ?? 'no'),
            'expiry_date' => $this->parseDate($row['expiry_date']),
            'is_active' => $this->parseBoolean($row['is_active'] ?? 'true'),
            'description' => $row['description'] ?? null
        ]);

        $medicine->save();
        $this->importedCount++;
        return $medicine;
    }

    /**
     * Update existing medicine
     */
    private function updateMedicine($medicine, $row, $categoryId)
    {
        $medicine->update([
            'name' => $row['name'],
            'generic_name' => $row['generic_name'] ?? $medicine->generic_name,
            'manufacturer' => $row['manufacturer'] ?? $medicine->manufacturer,
            'category_id' => $categoryId,
            'strength' => $row['strength'],
            'form' => $row['form'],
            'unit' => $row['unit'] ?? $medicine->unit,
            'barcode' => $row['barcode'] ?? $medicine->barcode,
            'stock_quantity' => (int) $row['stock_quantity'],
            'reorder_level' => isset($row['reorder_level']) ? (int) $row['reorder_level'] : $medicine->reorder_level,
            'selling_price' => (float) $row['selling_price'],
            'cost_price' => (float) $row['cost_price'],
            'prescription_required' => $this->parseBoolean($row['prescription_required'] ?? $medicine->prescription_required),
            'expiry_date' => $this->parseDate($row['expiry_date']),
            'is_active' => $this->parseBoolean($row['is_active'] ?? $medicine->is_active),
            'description' => $row['description'] ?? $medicine->description
        ]);
    }

    /**
     * Get category ID from row data
     */
    private function getCategoryId($row)
    {
        if (isset($row['category_id']) && is_numeric($row['category_id'])) {
            $category = Category::find($row['category_id']);
            if ($category) {
                return $category->id;
            }
        }

        if (isset($row['category_name'])) {
            $category = Category::where('name', 'like', '%' . $row['category_name'] . '%')->first();
            if ($category) {
                return $category->id;
            }
        }

        return null;
    }

    /**
     * Parse boolean values from string
     */
    private function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['true', '1', 'yes', 'on', 'active']);
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
            'batch_number' => 'required|string|max:100',
            'strength' => 'required|string|max:100',
            'form' => 'required|string|max:100',
            'stock_quantity' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
            'category_id' => 'required|exists:categories,id'
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Medicine name is required',
            'batch_number.required' => 'Batch number is required',
            'strength.required' => 'Strength is required',
            'form.required' => 'Form is required',
            'stock_quantity.required' => 'Stock quantity is required',
            'selling_price.required' => 'Selling price is required',
            'cost_price.required' => 'Cost price is required',
            'expiry_date.required' => 'Expiry date is required',
            'category_id.required' => 'Category ID is required',
            'category_id.exists' => 'Category does not exist'
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
