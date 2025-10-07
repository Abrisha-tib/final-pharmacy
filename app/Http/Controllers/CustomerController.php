<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with search and filtering.
     */
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->search($searchTerm);
            }

            // Apply status filter
            if ($request->has('status') && $request->status && $request->status !== 'All Status') {
                $query->where('status', $request->status);
            }

            // Apply segment filter
            if ($request->has('segment') && $request->segment && $request->segment !== 'All Segments') {
                $query->where('segment', $request->segment);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = 12;
            $customers = $query->paginate($perPage);

            // Get statistics
            $stats = $this->getCustomerStats();

            return view('customers', compact('customers', 'stats', 'request'));
            
        } catch (\Exception $e) {
            \Log::error('Customer index error: ' . $e->getMessage());
            return view('customers', [
                'customers' => collect(),
                'stats' => $this->getEmptyStats(),
                'request' => $request
            ]);
        }
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'status' => 'nullable|in:new,active,inactive,premium',
            'segment' => 'nullable|in:new,regular,loyal,vip',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Customer creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer'
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Add calculated attributes to the response
        $customerData = $customer->toArray();
        $customerData['total_sales'] = $customer->total_sales;
        $customerData['average_order_value'] = $customer->average_order_value;
        
        return response()->json($customerData);
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        \Log::info('Customer update request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'date_of_birth' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'status' => 'nullable|in:active,inactive,pending',
            'segment' => 'nullable|in:new,regular,loyal,vip',
            'notes' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            \Log::error('Customer update validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'customer' => $customer
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Customer update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer'
            ], 500);
        }
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Customer deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer'
            ], 500);
        }
    }

    /**
     * Get customer statistics.
     */
    public function getStats()
    {
        try {
            $stats = $this->getCustomerStats();
            return response()->json($stats);
        } catch (\Exception $e) {
            \Log::error('Customer stats error: ' . $e->getMessage());
            return response()->json($this->getEmptyStats());
        }
    }

    /**
     * Get customer statistics data.
     */
    private function getCustomerStats()
    {
        try {
            $totalCustomers = Customer::count();
            $activeCustomers = Customer::active()->count();
            $premiumCustomers = Customer::premium()->count();
            $totalRevenue = Customer::sum('total_spent');

            return [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'premium_customers' => $premiumCustomers,
                'total_revenue' => $totalRevenue
            ];
        } catch (\Exception $e) {
            \Log::error('Customer stats calculation error: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    /**
     * Get empty statistics for error handling.
     */
    private function getEmptyStats()
    {
        return [
            'total_customers' => 0,
            'active_customers' => 0,
            'premium_customers' => 0,
            'total_revenue' => 0
        ];
    }

    /**
     * Batch add customers from CSV/Excel.
     */
    public function batchAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customers' => 'required|array',
            'customers.*.name' => 'required|string|max:255',
            'customers.*.email' => 'required|email',
            'customers.*.phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $createdCustomers = [];
            foreach ($request->customers as $customerData) {
                $customer = Customer::create($customerData);
                $createdCustomers[] = $customer;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($createdCustomers) . ' customers created successfully',
                'customers' => $createdCustomers
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Batch customer creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customers'
            ], 500);
        }
    }

    /**
     * Handle customer import
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
            $fileName = 'customer_import_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName);

            // Import the data
            $import = new CustomerImport($importMode);
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
            Log::error('Customer import failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle customer export
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
            $filename = $request->input('filename', 'customers_export');
            $filters = $request->input('filters', []);

            // Generate filename with timestamp
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $fullFilename = $filename . '_' . $timestamp;

            $export = new CustomerExport($filters);

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
            Log::error('Customer export failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download customer import template
     */
    public function downloadTemplate(): Response
    {
        try {
            $templateData = [
                [
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
                    'Insurance Number'
                ],
                [
                    'John Doe',
                    'john.doe@example.com',
                    '+1234567890',
                    '123 Main St',
                    'New York',
                    'USA',
                    '30',
                    '100',
                    '500.00',
                    'active',
                    'regular',
                    'Regular customer',
                    '1993-05-15',
                    'male',
                    'Jane Doe - +1234567891',
                    'None',
                    'None',
                    'Health Insurance Co',
                    'INS123456'
                ]
            ];

            $export = new CustomerExport([]);
            $export->setTemplateData($templateData);

            return Excel::download($export, 'customer_import_template.xlsx');

        } catch (\Exception $e) {
            Log::error('Customer template download failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Template download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer export statistics
     */
    public function getExportStats(Request $request): JsonResponse
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Customer::query();

            // Apply filters
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['segment']) && $filters['segment']) {
                $query->where('segment', $filters['segment']);
            }

            if (isset($filters['city']) && $filters['city']) {
                $query->where('city', 'like', '%' . $filters['city'] . '%');
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            if (isset($filters['min_spent']) && $filters['min_spent']) {
                $query->where('total_spent', '>=', $filters['min_spent']);
            }

            if (isset($filters['max_spent']) && $filters['max_spent']) {
                $query->where('total_spent', '<=', $filters['max_spent']);
            }

            $totalRecords = $query->count();
            $activeCustomers = $query->where('status', 'active')->count();
            $premiumCustomers = $query->where('segment', 'vip')->count();
            $totalValue = $query->sum('total_spent');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_records' => $totalRecords,
                    'active_customers' => $activeCustomers,
                    'premium_customers' => $premiumCustomers,
                    'total_value' => number_format($totalValue, 2)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Customer export stats failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get export statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer print preview data
     */
    public function getPrintPreview(Request $request): JsonResponse
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Customer::query();

            // Apply filters
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['segment']) && $filters['segment']) {
                $query->where('segment', $filters['segment']);
            }

            if (isset($filters['city']) && $filters['city']) {
                $query->where('city', 'like', '%' . $filters['city'] . '%');
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $customers = $query->orderBy('name')->limit(3)->get(); // Get first 3 for preview
            
            // Calculate statistics
            $totalCustomers = $query->count();
            $activeCustomers = $query->where('status', 'active')->count();
            $premiumCustomers = $query->where('segment', 'vip')->count();
            $totalValue = $query->sum('total_spent');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_records' => $totalCustomers,
                    'active_customers' => $activeCustomers,
                    'premium_customers' => $premiumCustomers,
                    'total_value' => number_format($totalValue, 2),
                    'preview_customers' => $customers->map(function($customer) {
                        return [
                            'name' => $customer->name,
                            'email' => $customer->email,
                            'phone' => $customer->phone,
                            'city' => $customer->city,
                            'status' => $customer->status,
                            'segment' => $customer->segment,
                            'total_spent' => number_format($customer->total_spent, 2)
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Customer print preview failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get print preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate customer print report
     */
    public function printReport(Request $request)
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Customer::query();

            // Apply filters
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['segment']) && $filters['segment']) {
                $query->where('segment', $filters['segment']);
            }

            if (isset($filters['city']) && $filters['city']) {
                $query->where('city', 'like', '%' . $filters['city'] . '%');
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $customers = $query->orderBy('name')->get();
            
            // Calculate statistics
            $totalCustomers = $customers->count();
            $activeCustomers = $customers->where('status', 'active')->count();
            $premiumCustomers = $customers->where('segment', 'vip')->count();
            $totalValue = $customers->sum('total_spent');

            return view('print.customer-report', compact('customers', 'totalCustomers', 'activeCustomers', 'premiumCustomers', 'totalValue'));

        } catch (\Exception $e) {
            Log::error('Customer print report failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate print report: ' . $e->getMessage()
            ], 500);
        }
    }
}
