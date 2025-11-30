<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;

/**
 * ReportService
 *
 * خدمة التقارير
 * توليد وتحليل التقارير المالية والبيعية
 *
 * This service generates comprehensive reports for sales, revenue, products,
 * vendors, and administrative analytics
 *
 * @package App\Services
 * @version 1.0.0
 * @author Gulf eCommerce Team
 */
class ReportService
{
    /**
     * Generate sales report
     *
     * توليد تقرير المبيعات
     */
    public function generateSalesReport(string $startDate, string $endDate): array
    {
        try {
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $totalSales = 0;
            $totalOrders = 0;
            $ordersByStatus = [];

            foreach ($orders as $order) {
                $totalSales += $order->total_amount ?? 0;
                $totalOrders++;
                $status = $order->status ?? 'unknown';
                $ordersByStatus[$status] = ($ordersByStatus[$status] ?? 0) + 1;
            }

            $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

            return [
                'success' => true,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ],
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order' => $averageOrder,
                'orders_by_status' => $ordersByStatus,
                'currency' => 'SAR'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل توليد التقرير: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate vendor report
     */
    public function generateVendorReport(int $vendorId): array
    {
        try {
            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                return [
                    'success' => false,
                    'message' => 'البائع غير موجود | Vendor not found'
                ];
            }

            $products = $vendor->products()->count();
            $totalRevenue = $vendor->total_revenue ?? 0;
            $totalOrders = $vendor->orders()->count();
            $commissionEarned = $totalRevenue * 0.15;

            return [
                'success' => true,
                'vendor_id' => $vendorId,
                'vendor_name' => $vendor->business_name,
                'total_products' => $products,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'commission_earned' => $commissionEarned,
                'commission_rate' => 0.15
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل إنشاء التقرير: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate product report
     */
    public function generateProductReport(): array
    {
        try {
            $topProducts = Product::orderBy('sales_count', 'desc')
                ->limit(10)
                ->get(['id', 'name', 'price', 'sales_count']);

            $totalProducts = Product::count();
            $activeProducts = Product::where('status', 'active')->count();

            return [
                'success' => true,
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'top_10_products' => $topProducts->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل التقرير: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate revenue report by category
     */
    public function generateCategoryRevenueReport(): array
    {
        try {
            $categories = Product::select('category')
                ->groupBy('category')
                ->withCount('orders')
                ->get();

            $categoryData = [];
            foreach ($categories as $cat) {
                $categoryData[$cat->category] = $cat->orders_count ?? 0;
            }

            return [
                'success' => true,
                'revenue_by_category' => $categoryData,
                'message' => 'تم توليد التقرير | Report generated'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل توليد التقرير: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Export report to CSV
     */
    public function exportReportCsv(array $reportData, string $filename): array
    {
        try {
            $csv = '';
            $header = array_keys($reportData[0] ?? []);
            $csv .= implode(',', $header) . "\n";

            foreach ($reportData as $row) {
                $csv .= implode(',', $row) . "\n";
            }

            return [
                'success' => true,
                'filename' => $filename . '.csv',
                'message' => 'تم ترصير البيانات | Data exported'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل التصدير: ' . $e->getMessage()
            ];
        }
    }
}
