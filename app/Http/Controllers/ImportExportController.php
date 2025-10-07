<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Exports\MedicineExport;
use App\Imports\MedicineImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportExportController extends Controller
{
    /**
     * Display the import/export modal
     */
    public function index()
    {
        return view('inventory'); // This will be handled by the existing inventory route
    }

    /**
     * Handle file import
     */
    public function import(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
                'import_mode' => 'required|in:create,update,replace'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $importMode = $request->input('import_mode', 'create');

            // Store the file temporarily
            $fileName = 'import_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName);

            // Import the data
            $import = new MedicineImport($importMode);
            Excel::import($import, $filePath);

            // Clean up temporary file
            Storage::delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'File imported successfully',
                'data' => [
                    'imported_count' => $import->getImportedCount(),
                    'skipped_count' => $import->getSkippedCount(),
                    'error_count' => $import->getErrorCount(),
                    'errors' => $import->getErrors()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle data export
     */
    public function export(Request $request): Response
    {
        try {
            $validator = Validator::make($request->all(), [
                'format' => 'required|in:excel,csv,pdf',
                'filename' => 'nullable|string|max:255',
                'filters' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $format = $request->input('format', 'excel');
            $filename = $request->input('filename', 'medicines_export');
            $filters = $request->input('filters', []);

            // Generate filename with timestamp
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $fullFilename = $filename . '_' . $timestamp;

            $export = new MedicineExport($filters);

            switch ($format) {
                case 'excel':
                    return Excel::download($export, $fullFilename . '.xlsx');
                case 'csv':
                    return Excel::download($export, $fullFilename . '.csv', \Maatwebsite\Excel\Excel::CSV);
                case 'pdf':
                    return Excel::download($export, $fullFilename . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
                default:
                    return Excel::download($export, $fullFilename . '.xlsx');
            }

        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate(): Response
    {
        try {
            $templateData = [
                [
                    'Name*',
                    'Generic Name',
                    'Manufacturer',
                    'Category ID*',
                    'Strength*',
                    'Form*',
                    'Unit',
                    'Barcode',
                    'Batch Number*',
                    'Stock Quantity*',
                    'Reorder Level',
                    'Selling Price*',
                    'Cost Price*',
                    'Prescription Required (yes/no)',
                    'Expiry Date* (YYYY-MM-DD)',
                    'Is Active (true/false)',
                    'Description'
                ],
                [
                    'Paracetamol',
                    'Acetaminophen',
                    'ABC Pharma',
                    '1',
                    '500mg',
                    'Tablet',
                    'Box',
                    '1234567890123',
                    'B2025001',
                    '100',
                    '20',
                    '25.50',
                    '20.00',
                    'no',
                    '2025-12-31',
                    'true',
                    'Pain relief medication'
                ]
            ];

            $export = new MedicineExport([]);
            $export->setTemplateData($templateData);

            return Excel::download($export, 'medicine_import_template.xlsx');

        } catch (\Exception $e) {
            Log::error('Template download failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Template download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get export statistics
     */
    public function getExportStats(Request $request): JsonResponse
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Medicine::with('category');

            // Apply filters
            if (isset($filters['category_id']) && $filters['category_id']) {
                $query->where('category_id', $filters['category_id']);
            }

            if (isset($filters['stock_status']) && $filters['stock_status']) {
                switch ($filters['stock_status']) {
                    case 'in_stock':
                        $query->where('stock_quantity', '>', 10);
                        break;
                    case 'low_stock':
                        $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', '<=', 0);
                        break;
                }
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $totalRecords = $query->count();
            $lowStockItems = $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
            $outOfStockItems = $query->where('stock_quantity', '<=', 0)->count();
            
            $totalValue = $query->get()->sum(function($medicine) {
                return (float)$medicine->selling_price * (int)$medicine->stock_quantity;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_records' => $totalRecords,
                    'low_stock_items' => $lowStockItems,
                    'out_of_stock_items' => $outOfStockItems,
                    'total_value' => number_format($totalValue, 2)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Export stats failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get export statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories for export filters
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Category::active()->ordered()->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            Log::error('Get categories failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get print preview data
     */
    public function getPrintPreview(Request $request): JsonResponse
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Medicine::with('category');

            // Apply filters
            if (isset($filters['category_id']) && $filters['category_id']) {
                $query->where('category_id', $filters['category_id']);
            }

            if (isset($filters['stock_status']) && $filters['stock_status']) {
                switch ($filters['stock_status']) {
                    case 'in_stock':
                        $query->where('stock_quantity', '>', 10);
                        break;
                    case 'low_stock':
                        $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', '<=', 0);
                        break;
                }
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $medicines = $query->orderBy('name')->limit(3)->get(); // Get first 3 for preview
            
            // Calculate statistics - get fresh query for accurate counts
            $totalItems = $query->count();
            $lowStockItems = $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
            $outOfStockItems = $query->where('stock_quantity', '<=', 0)->count();
            
            // Get all medicines for accurate total value calculation
            $allMedicines = $query->get();
            $totalValue = $allMedicines->sum(function($medicine) {
                return (float)$medicine->selling_price * (int)$medicine->stock_quantity;
            });
            
            // Debug logging
            \Log::info('Print Preview Calculation', [
                'total_medicines' => $allMedicines->count(),
                'total_value' => $totalValue,
                'sample_medicine' => $allMedicines->first() ? [
                    'name' => $allMedicines->first()->name,
                    'selling_price' => $allMedicines->first()->selling_price,
                    'stock_quantity' => $allMedicines->first()->stock_quantity,
                    'calculated_value' => (float)$allMedicines->first()->selling_price * (int)$allMedicines->first()->stock_quantity
                ] : null
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_records' => $totalItems,
                    'low_stock_items' => $lowStockItems,
                    'out_of_stock_items' => $outOfStockItems,
                    'total_value' => number_format($totalValue, 2),
                    'preview_medicines' => $medicines->map(function($medicine) {
                        $status = $medicine->stock_quantity <= 0 ? 'out-of-stock' : 
                                 ($medicine->stock_quantity <= 10 ? 'low-stock' : 'in-stock');
                        $statusText = $medicine->stock_quantity <= 0 ? 'Out of Stock' : 
                                     ($medicine->stock_quantity <= 10 ? 'Low Stock' : 'In Stock');
                        
                        return [
                            'name' => $medicine->name,
                            'category' => $medicine->category->name ?? 'N/A',
                            'stock_quantity' => $medicine->stock_quantity,
                            'selling_price' => number_format($medicine->selling_price, 2),
                            'status' => $status,
                            'status_text' => $statusText
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Print preview failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get print preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate print report
     */
    public function printReport(Request $request)
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Medicine::with('category');

            // Apply filters
            if (isset($filters['category_id']) && $filters['category_id']) {
                $query->where('category_id', $filters['category_id']);
            }

            if (isset($filters['stock_status']) && $filters['stock_status']) {
                switch ($filters['stock_status']) {
                    case 'in_stock':
                        $query->where('stock_quantity', '>', 10);
                        break;
                    case 'low_stock':
                        $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', '<=', 0);
                        break;
                }
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $medicines = $query->orderBy('name')->get();
            
            // Calculate statistics
            $totalItems = $medicines->count();
            $lowStockItems = $medicines->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
            $outOfStockItems = $medicines->where('stock_quantity', '<=', 0)->count();
            $totalValue = $medicines->sum(function($medicine) {
                return (float)$medicine->selling_price * (int)$medicine->stock_quantity;
            });

            return view('print.inventory-report', compact('medicines', 'totalItems', 'lowStockItems', 'outOfStockItems', 'totalValue'));

        } catch (\Exception $e) {
            Log::error('Print report failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate print report: ' . $e->getMessage()
            ], 500);
        }
    }
}
