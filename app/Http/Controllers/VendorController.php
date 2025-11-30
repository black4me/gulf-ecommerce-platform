<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * VendorController - معالج البائع
 * 
 * يدير بيانات البائع واللوحة والإحصائيات
 * Handles vendor profile, dashboard and analytics
 */
class VendorController extends Controller
{
    /**
     * Constructor - البناء
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('vendor');
    }

    /**
     * Get vendor profile - ملف البائع
     * 
     * GET /api/vendor/profile
     * 
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم عثور بائع | Vendor not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'ملف البائع | Vendor profile',
                'data' => $vendor
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التلامس | Failed to fetch profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update vendor profile - تحديث بيانات البائع
     * 
     * PUT /api/vendor/profile
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'shop_name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:500',
                'city' => 'sometimes|string|max:100',
                'country' => 'sometimes|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق | Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();
            $vendor->update($request->only(['shop_name', 'description', 'phone', 'address', 'city', 'country']));

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث البيانات | Profile updated successfully',
                'data' => $vendor
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحديث | Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor dashboard - لوحة البائع
     * 
     * GET /api/vendor/dashboard
     * 
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();

            // Fetch statistics
            $totalOrders = Order::whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->count();

            $totalRevenue = Order::whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->where('status', 'delivered')->sum('total');

            $pendingOrders = Order::whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->where('status', 'pending')->count();

            $totalProducts = $vendor->products()->count();

            return response()->json([
                'success' => true,
                'message' => 'لوحة البائع | Vendor dashboard',
                'data' => [
                    'vendor_id' => $vendor->id,
                    'shop_name' => $vendor->shop_name,
                    'total_orders' => $totalOrders,
                    'total_revenue' => $totalRevenue,
                    'pending_orders' => $pendingOrders,
                    'total_products' => $totalProducts,
                    'commission_rate' => $vendor->commission_rate,
                    'status' => $vendor->status
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحميل اللوحة | Failed to load dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor's sales analytics - الإحصائيات
     * 
     * GET /api/vendor/analytics
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();
            $period = $request->input('period', 'monthly'); // daily, weekly, monthly, yearly

            // Calculate date range based on period
            $startDate = match($period) {
                'daily' => now()->startOfDay(),
                'weekly' => now()->startOfWeek(),
                'monthly' => now()->startOfMonth(),
                'yearly' => now()->startOfYear(),
                default => now()->startOfMonth()
            };

            $orders = Order::whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->whereBetween('created_at', [$startDate, now()])->get();

            $totalSales = $orders->sum('total');
            $ordersCount = $orders->count();
            $averageOrder = $ordersCount > 0 ? $totalSales / $ordersCount : 0;

            return response()->json([
                'success' => true,
                'message' => 'الإحصائيات | Analytics',
                'data' => [
                    'period' => $period,
                    'total_sales' => $totalSales,
                    'orders_count' => $ordersCount,
                    'average_order_value' => $averageOrder,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => now()->toDateString()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحميل الإحصائيات | Failed to fetch analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor's orders - طلبات البائع
     * 
     * GET /api/vendor/orders
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function orders(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);
            $status = $request->input('status');

            $query = Order::whereHas('items', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });

            if ($status) {
                $query->where('status', $status);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'طلبات البائع | Vendor orders',
                'data' => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاسترجاع | Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get withdrawal history - سجل الاسححاب
     * 
     * GET /api/vendor/withdrawals
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function withdrawals(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);

            // Fetch withdrawals if withdrawal table exists
            $withdrawals = collect();

            return response()->json([
                'success' => true,
                'message' => 'سجل الاسححاب | Withdrawal history',
                'data' => [
                    'current_balance' => $vendor->wallet_balance ?? 0,
                    'withdrawals' => $withdrawals
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاسترجاع | Failed to fetch withdrawals',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
