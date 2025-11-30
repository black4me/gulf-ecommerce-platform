<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * OrderController - معالج الطلبات
 * 
 * يدير عمليات إنشاء وعرض وتراقبة الطلبات
 * Handles order creation, viewing, and tracking operations
 */
class OrderController extends Controller
{
    /**
     * OrderService instance
     * @var OrderService
     */
    protected $orderService;

    /**
     * Constructor - البناء
     * 
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('auth:api');
    }

    /**
     * Get current user's orders - طلبات المستخدم
     * 
     * GET /api/orders
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);
            $status = $request->input('status');

            $query = Order::where('user_id', $user->id);
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'قائمة الطلبات | Orders list',
                'data' => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التلامس | Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single order details - تفاصيل الطلب
     * 
     * GET /api/orders/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth('api')->user();

            // Check if user is owner or admin
            if ($order->user_id !== $user->id && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            $order->load('items', 'items.product', 'payment');

            return response()->json([
                'success' => true,
                'message' => 'تفاصيل الطلب | Order details',
                'data' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب غير موجود | Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new order - إنشاء طلب جديد
     * 
     * POST /api/orders
     * 
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            
            $order = $this->orderService->createOrder(
                $user->id,
                $request->input('items'),
                $request->input('shipping_address'),
                $request->input('payment_method')
            );

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الطلب بنجاح | Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء الطلب | Order creation failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel order - إلغاء الطلب
     * 
     * POST /api/orders/{id}/cancel
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth('api')->user();

            if ($order->user_id !== $user->id && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إلغاء الطلب | Cannot cancel order in this status'
                ], 400);
            }

            $this->orderService->cancelOrder($id);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الطلب بنجاح | Order cancelled successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إلغاء الطلب | Order cancellation failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update order status - تحديث حالة الطلب
     * 
     * PUT /api/orders/{id}/status
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth('api')->user();

            if ($user->role !== 'admin' && $user->role !== 'vendor') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            $status = $request->input('status');
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'حالة غير صحيحة | Invalid status'
                ], 400);
            }

            $order->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب | Order status updated',
                'data' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث حالة الطلب | Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track order - تراقبة الطلب
     * 
     * GET /api/orders/{id}/track
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function track(int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth('api')->user();

            if ($order->user_id !== $user->id && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'تراقبة الطلب | Order tracking',
                'data' => [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'total' => $order->total,
                    'items_count' => $order->items()->count()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تراقبة الطلب | Tracking failed',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
