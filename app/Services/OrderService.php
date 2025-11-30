<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * OrderService - لمخدمة الطلبات
 * 
 * Handles order creation, updates, processing, and management
 * معالجة إنشاء الطلبات وتحديثها ومعالجتها
 */
class OrderService
{
    /**
     * Create a new order from cart
     * 
     * @param int $userId
     * @param array $orderData
     * @return Order
     */
    public function createOrder(int $userId, array $orderData): Order
    {
        return DB::transaction(function () use ($userId, $orderData) {
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'subtotal' => $orderData['subtotal'] ?? 0,
                'tax_amount' => $orderData['tax_amount'] ?? 0,
                'shipping_cost' => $orderData['shipping_cost'] ?? 0,
                'discount_amount' => $orderData['discount_amount'] ?? 0,
                'total_amount' => $orderData['total_amount'] ?? 0,
                'currency' => $orderData['currency'] ?? 'SAR',
                'shipping_address' => $orderData['shipping_address'] ?? null,
                'billing_address' => $orderData['billing_address'] ?? null,
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Add order items
            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'vendor_id' => $item['vendor_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'status' => 'pending',
                ]);
            }

            Log::info('Order created', ['order_id' => $order->id, 'user_id' => $userId]);

            return $order;
        });
    }

    /**
     * Update order status
     * 
     * @param Order $order
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(Order $order, string $status): bool
    {
        $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $order->update(['status' => $status]);
    }

    /**
     * Update payment status
     * 
     * @param Order $order
     * @param string $status
     * @return bool
     */
    public function updatePaymentStatus(Order $order, string $status): bool
    {
        $validStatuses = ['unpaid', 'pending', 'paid', 'failed', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $order->update(['payment_status' => $status]);
    }

    /**
     * Cancel an order
     * 
     * @param Order $order
     * @param string $reason
     * @return bool
     */
    public function cancelOrder(Order $order, string $reason = ''): bool
    {
        return DB::transaction(function () use ($order, $reason) {
            if ($order->status === 'cancelled') {
                return false;
            }

            // Cancel associated items
            foreach ($order->items as $item) {
                $item->update(['status' => 'cancelled']);
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            Log::info('Order cancelled', ['order_id' => $order->id, 'reason' => $reason]);

            return true;
        });
    }

    /**
     * Get order by order number
     * 
     * @param string $orderNumber
     * @return Order|null
     */
    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)->first();
    }

    /**
     * Get user orders
     * 
     * @param int $userId
     * @param int $perPage
     * @return mixed
     */
    public function getUserOrders(int $userId, int $perPage = 15)
    {
        return Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get vendor orders
     * 
     * @param int $vendorId
     * @param int $perPage
     * @return mixed
     */
    public function getVendorOrders(int $vendorId, int $perPage = 15)
    {
        return OrderItem::where('vendor_id', $vendorId)
            ->with('order', 'product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Calculate order summary
     * 
     * @param Collection $items
     * @param float $shippingCost
     * @param float $taxRate
     * @return array
     */
    public function calculateOrderSummary(Collection $items, float $shippingCost = 0, float $taxRate = 0.15): array
    {
        $subtotal = $items->sum(fn ($item) => $item['quantity'] * $item['unit_price']);
        $taxAmount = $subtotal * $taxRate;
        $total = $subtotal + $taxAmount + $shippingCost;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'shipping_cost' => round($shippingCost, 2),
            'total_amount' => round($total, 2),
        ];
    }

    /**
     * Generate unique order number
     * 
     * @return string
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHi');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }
}
