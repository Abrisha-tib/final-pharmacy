<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedMedicineController extends Controller
{
    /**
     * Display a listing of medicines with advanced optimization
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'medicines_' . md5(serialize($request->all()));
        
        return Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Medicine::with(['category:id,name,color'])
                ->select([
                    'id', 'name', 'generic_name', 'category_id', 'strength', 'form',
                    'stock_quantity', 'selling_price', 'cost_price', 'is_active',
                    'expiry_date', 'batch_number', 'created_at'
                ]);

            // Optimized search using full-text search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereRaw("MATCH(name, generic_name) AGAINST(? IN BOOLEAN MODE)", [$search])
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('generic_name', 'like', "%{$search}%")
                      ->orWhere('batch_number', 'like', "%{$search}%");
                });
            }

            // Optimized filters
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('status') && $request->status) {
                $query->where('is_active', $request->status === 'active');
            }

            if ($request->has('stock') && $request->stock) {
                switch ($request->stock) {
                    case 'in_stock':
                        $query->where('stock_quantity', '>', 0);
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', '<=', 0);
                        break;
                    case 'low_stock':
                        $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                        break;
                }
            }

            // Optimized ordering
            $query->orderBy('is_active', 'desc')
                  ->orderBy('name', 'asc');

            $perPage = min($request->get('per_page', 12), 100); // Limit max per page
            $medicines = $query->paginate($perPage);

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
        });
    }

    /**
     * Get medicine statistics with caching
     */
    public function statistics(): JsonResponse
    {
        return Cache::remember('medicine_statistics', 600, function () {
            $stats = DB::table('medicines')
                ->selectRaw('
                    COUNT(*) as total_medicines,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_medicines,
                    SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) as in_stock,
                    SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 10 THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_soon,
                    SUM(CASE WHEN expiry_date < NOW() THEN 1 ELSE 0 END) as expired,
                    SUM(selling_price * stock_quantity) as total_value
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        });
    }

    /**
     * Search medicines with autocomplete
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $cacheKey = 'medicine_autocomplete_' . md5($search);
        
        return Cache::remember($cacheKey, 300, function () use ($search) {
            $medicines = Medicine::select('id', 'name', 'generic_name', 'stock_quantity')
                ->where('is_active', true)
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('generic_name', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $medicines
            ]);
        });
    }

    /**
     * Get medicines for virtual scrolling
     */
    public function virtual(Request $request): JsonResponse
    {
        $start = $request->get('start', 0);
        $limit = min($request->get('limit', 50), 100);
        
        $medicines = Medicine::with(['category:id,name,color'])
            ->select(['id', 'name', 'generic_name', 'category_id', 'stock_quantity', 'selling_price', 'is_active'])
            ->where('is_active', true)
            ->orderBy('name')
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    /**
     * Clear medicine cache
     */
    public function clearCache(): JsonResponse
    {
        Cache::tags(['medicines'])->flush();
        
        return response()->json([
            'success' => true,
            'message' => 'Medicine cache cleared successfully'
        ]);
    }
}
