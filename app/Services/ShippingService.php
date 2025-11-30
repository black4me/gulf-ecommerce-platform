<?php

namespace App\Services;

use App\Models\Order;

/**
 * ShippingService
 *
 * خدمة الشحن
 * توفر هذه الخدمة وظائف إدارة الشحنات وحساب رسوم النقل
 *
 * This service manages shipping operations, cost calculations, carrier integrations
 *
 * @package App\Services
 * @version 1.0.0
 * @author Gulf eCommerce Team
 */
class ShippingService
{
    private const SHIPPING_ZONES = [
        'SA' => 'Saudi Arabia',
        'AE' => 'United Arab Emirates',
        'KW' => 'Kuwait',
        'QA' => 'Qatar',
        'OM' => 'Oman'
    ];

    private const BASE_RATES = [
        'SA' => 15,
        'AE' => 20,
        'KW' => 25,
        'QA' => 25,
        'OM' => 30
    ];

    /**
     * Calculate shipping cost
     *
     * حساب تكلفة الشحن
     */
    public function calculateShippingCost(int $orderId, string $destination, float $weight): array
    {
        try {
            if (!array_key_exists($destination, self::SHIPPING_ZONES)) {
                return [
                    'success' => false,
                    'message' => 'الوجهة غير مدعومة | Destination not supported'
                ];
            }

            $baseRate = self::BASE_RATES[$destination];
            $weightCharge = $weight > 1 ? ($weight - 1) * 5 : 0;
            $totalCost = $baseRate + $weightCharge;

            return [
                'success' => true,
                'cost' => $totalCost,
                'currency' => 'SAR',
                'message' => 'تم حساب تكلفة الشحن | Shipping cost calculated'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في حساب: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create shipment
     *
     * إنشاء شحنة
     */
    public function createShipment(int $orderId, array $shipmentData): array
    {
        try {
            $shipmentId = 'SHIP_' . uniqid();
            $trackingNumber = $this->generateTrackingNumber();

            $order = Order::find($orderId);
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'الطلب غير موجود | Order not found'
                ];
            }

            $order->update([
                'shipment_id' => $shipmentId,
                'tracking_number' => $trackingNumber,
                'status' => 'shipped'
            ]);

            return [
                'success' => true,
                'shipment_id' => $shipmentId,
                'tracking_number' => $trackingNumber,
                'message' => 'تم إنشاء الشحنة | Shipment created'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل إنشاء: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate tracking number
     */
    private function generateTrackingNumber(): string
    {
        $timestamp = time();
        $random = rand(10000, 99999);
        return 'TRACK-' . $timestamp . '-' . $random;
    }

    /**
     * Update shipment status
     */
    public function updateShipmentStatus(int $orderId, string $status): array
    {
        try {
            $validStatuses = ['in_transit', 'out_for_delivery', 'delivered', 'failed'];

            if (!in_array($status, $validStatuses)) {
                return [
                    'success' => false,
                    'message' => 'حالة غير صحيحة | Invalid status'
                ];
            }

            $order = Order::find($orderId);
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'الطلب غير موجود | Order not found'
                ];
            }

            $order->update(['shipping_status' => $status]);

            return [
                'success' => true,
                'message' => 'تم تحديث حالة الشحنة | Status updated'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل التحديث: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Estimate delivery date
     */
    public function estimateDeliveryDate(string $destination): array
    {
        $deliveryDays = [
            'SA' => 2,
            'AE' => 3,
            'KW' => 3,
            'QA' => 3,
            'OM' => 4
        ];

        $days = $deliveryDays[$destination] ?? 5;
        $estimatedDate = date('Y-m-d', strtotime("+{$days} days"));

        return [
            'estimated_days' => $days,
            'delivery_date' => $estimatedDate,
            'destination' => self::SHIPPING_ZONES[$destination] ?? $destination
        ];
    }

    /**
     * Get available shipping zones
     */
    public function getAvailableZones(): array
    {
        return self::SHIPPING_ZONES;
    }

    /**
     * Get shipping rates
     */
    public function getShippingRates(): array
    {
        $rates = [];
        foreach (self::SHIPPING_ZONES as $code => $zone) {
            $rates[$code] = [
                'zone' => $zone,
                'base_rate' => self::BASE_RATES[$code],
                'currency' => 'SAR'
            ];
        }
        return $rates;
    }
}
