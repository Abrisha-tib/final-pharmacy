<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MedicineController extends Controller
{
    /**
     * Display a listing of medicines
     */
    public function index(Request $request): JsonResponse
    {
        $query = Medicine::with('category');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Stock filter
        if ($request->has('stock') && $request->stock) {
            if ($request->stock === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock === 'low_stock') {
                $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
            }
        }

        $perPage = $request->get('per_page', 12);
        $medicines = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $medicines->items(),
            'pagination' => [
                'current_page' => $medicines->currentPage(),
                'last_page' => $medicines->lastPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total(),
                'from' => $medicines->firstItem(),
                'to' => $medicines->lastItem(),
                'has_more_pages' => $medicines->hasMorePages(),
                'prev_page_url' => $medicines->previousPageUrl(),
                'next_page_url' => $medicines->nextPageUrl()
            ]
        ]);
    }

    /**
     * Store a newly created medicine
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'strength' => 'required|string|max:100',
            'form' => 'required|string|max:100',
            'unit' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:50',
            'batch_number' => 'required|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'prescription_required' => 'nullable|in:yes,no',
            'expiry_date' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $medicine = Medicine::create([
                'name' => $request->name,
                'generic_name' => $request->generic_name,
                'manufacturer' => $request->manufacturer,
                'category_id' => $request->category_id,
                'strength' => $request->strength,
                'form' => $request->form,
                'unit' => $request->unit,
                'barcode' => $request->barcode,
                'batch_number' => $request->batch_number,
                'stock_quantity' => $request->stock_quantity,
                'reorder_level' => $request->reorder_level,
                'selling_price' => $request->selling_price,
                'cost_price' => $request->cost_price,
                'prescription_required' => $request->prescription_required,
                'expiry_date' => $request->expiry_date,
                'is_active' => $request->boolean('is_active', true),
                'description' => $request->description
            ]);

            $medicine->load('category');

            // Clear specific caches instead of flushing all (better for shared hosting)
            // Note: Laravel doesn't support wildcards in Cache::forget, so we need to clear all possible cache keys
            $this->clearInventoryCaches();
            
            // Log cache clearing for debugging
            \Log::info('Medicine created - cache cleared', [
                'medicine_id' => $medicine->id,
                'medicine_name' => $medicine->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medicine created successfully',
                'data' => $medicine
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create medicine: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified medicine
     */
    public function show(Medicine $medicine): JsonResponse
    {
        // Cache individual medicine for 1 hour
        $cacheKey = 'medicine_' . $medicine->id;
        
        $medicineData = Cache::remember($cacheKey, 3600, function() use ($medicine) {
            return $medicine->load('category:id,name,color');
        });
        
        return response()->json([
            'success' => true,
            'data' => $medicineData
        ]);
    }

    /**
     * Update the specified medicine
     */
    public function update(Request $request, Medicine $medicine): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'strength' => 'required|string|max:100',
            'form' => 'required|string|max:100',
            'unit' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:50',
            'batch_number' => 'required|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'prescription_required' => 'nullable|in:yes,no',
            'expiry_date' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        $medicine->update([
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'manufacturer' => $request->manufacturer,
            'category_id' => $request->category_id,
            'strength' => $request->strength,
            'form' => $request->form,
            'unit' => $request->unit,
            'barcode' => $request->barcode,
            'batch_number' => $request->batch_number,
            'stock_quantity' => $request->stock_quantity,
            'reorder_level' => $request->reorder_level,
            'selling_price' => $request->selling_price,
            'cost_price' => $request->cost_price,
            'prescription_required' => $request->prescription_required,
            'expiry_date' => $request->expiry_date,
            'is_active' => $request->boolean('is_active', true),
            'description' => $request->description
        ]);

        $medicine->load('category');

        // Clear related caches - comprehensive approach
        Cache::forget('medicine_' . $medicine->id);
        
        // Clear specific caches instead of flushing all (better for shared hosting)
        $this->clearInventoryCaches();
        
        // Log cache clearing for debugging
        \Log::info('Medicine updated - cache cleared', [
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Medicine updated successfully',
            'data' => $medicine
        ]);
    }

    /**
     * Remove the specified medicine
     */
    public function destroy(Medicine $medicine): JsonResponse
    {
        $medicine->delete();

        // Clear related caches - comprehensive approach
        Cache::forget('medicine_' . $medicine->id);
        
        // Clear specific caches instead of flushing all (better for shared hosting)
        $this->clearInventoryCaches();
        
        // Log cache clearing for debugging
        \Log::info('Medicine deleted - cache cleared', [
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Medicine deleted successfully'
        ]);
    }

    /**
     * Toggle medicine status
     */
    public function toggleStatus(Medicine $medicine): JsonResponse
    {
        $medicine->update(['is_active' => !$medicine->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Medicine status updated successfully',
            'data' => $medicine
        ]);
    }

    /**
     * Get medicine statistics
     */
    public function statistics(): JsonResponse
    {
        $totalMedicines = Medicine::count();
        $activeMedicines = Medicine::active()->count();
        $inStockMedicines = Medicine::inStock()->count();
        $outOfStockMedicines = Medicine::where('stock_quantity', '<=', 0)->count();
        $lowStockMedicines = Medicine::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
        $expiringSoon = Medicine::expiringSoon()->count();
        $expiredMedicines = Medicine::where('expiry_date', '<', now())->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_medicines' => $totalMedicines,
                'active_medicines' => $activeMedicines,
                'in_stock' => $inStockMedicines,
                'out_of_stock' => $outOfStockMedicines,
                'low_stock' => $lowStockMedicines,
                'expiring_soon' => $expiringSoon,
                'expired' => $expiredMedicines
            ]
        ]);
    }

    /**
     * Clear all inventory-related caches
     */
    private function clearInventoryCaches()
    {
        // Clear all possible cache keys
        Cache::forget('categories_active');
        Cache::forget('pharmaceutical_forms');
        Cache::forget('pharmaceutical_units');
        
        // Clear the global inventory stats cache
        Cache::forget('inventory_stats_global');
        
        // Clear all possible inventory stats cache keys (legacy support)
        $commonParams = [
            '', '?', '?page=1', '?page=2', '?page=3',
            '?search=', '?category=', '?batch=', '?stock=',
            '?search=&category=&batch=&stock=',
            '?page=1&search=', '?page=1&category=', '?page=1&batch=', '?page=1&stock='
        ];
        
        foreach ($commonParams as $param) {
            $cacheKey = 'inventory_stats_' . md5($param);
            Cache::forget($cacheKey);
        }
        
        // Also clear medicines cache with common patterns
        foreach ($commonParams as $param) {
            $cacheKey = 'medicines_' . md5($param);
            Cache::forget($cacheKey);
        }
        
        \Log::info('Inventory caches cleared', [
            'cleared_keys' => count($commonParams) * 2 + 1
        ]);
    }

    /**
     * Server-Sent Events stream for real-time medicine updates
     */
    public function stream(Request $request)
    {
        // Note: SSE doesn't support custom headers, so we'll rely on session authentication

        return response()->stream(function () {
            $lastId = 0;
            
            while (true) {
                // Get medicines updated since last check
                $medicines = Medicine::with('category')
                    ->where('id', '>', $lastId)
                    ->orderBy('id')
                    ->get();

                if ($medicines->isNotEmpty()) {
                    $lastId = $medicines->max('id');
                    
                    $data = [
                        'medicines' => $medicines,
                        'timestamp' => now()->toISOString()
                    ];
                    
                    echo "data: " . json_encode($data) . "\n\n";
                }
                
                // Send heartbeat every 30 seconds
                echo "data: " . json_encode(['heartbeat' => now()->toISOString()]) . "\n\n";
                
                if (connection_aborted()) {
                    break;
                }
                
                sleep(5); // Check every 5 seconds
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Cache-Control'
        ]);
    }
}
