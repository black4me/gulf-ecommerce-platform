<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Product;

/**
 * ReviewService
 *
 * خدمة التعليقاتوالتقييمات
 * إدارة تعليقات العملاء وتقييمات المنتجات
 *
 * This service manages product reviews and ratings, including validation,
 * moderation, and rating calculations
 *
 * @package App\Services
 * @version 1.0.0
 * @author Gulf eCommerce Team
 */
class ReviewService
{
    /**
     * Create product review
     *
     * إنشاء تعليق على منتج
     */
    public function createReview(int $productId, int $userId, int $rating, string $comment): array
    {
        try {
            if ($rating < 1 || $rating > 5) {
                return [
                    'success' => false,
                    'message' => 'التقييم يجب أن يكون بين 1 و 5 | Rating must be between 1 and 5'
                ];
            }

            $product = Product::find($productId);
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'المنتج غير موجود | Product not found'
                ];
            }

            Review::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => $rating,
                'comment' => $comment,
                'status' => 'pending',
                'helpful_count' => 0
            ]);

            return [
                'success' => true,
                'message' => 'تم إضافة التعليق بنجاح | Review added successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الإضافة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Approve review
     */
    public function approveReview(int $reviewId): array
    {
        try {
            $review = Review::find($reviewId);
            if (!$review) {
                return [
                    'success' => false,
                    'message' => 'التعليق غير موجود | Review not found'
                ];
            }

            $review->update(['status' => 'approved']);

            return [
                'success' => true,
                'message' => 'تم الموافقة على التعليق | Review approved'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الموافقة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reject review
     */
    public function rejectReview(int $reviewId, string $reason = ''): array
    {
        try {
            $review = Review::find($reviewId);
            if (!$review) {
                return [
                    'success' => false,
                    'message' => 'التعليق غير موجود | Review not found'
                ];
            }

            $review->update([
                'status' => 'rejected',
                'rejection_reason' => $reason
            ]);

            return [
                'success' => true,
                'message' => 'تم رفض التعليق | Review rejected'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الرفض: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate product rating
     */
    public function calculateProductRating(int $productId): array
    {
        try {
            $reviews = Review::where('product_id', $productId)
                ->where('status', 'approved')
                ->get();

            if ($reviews->isEmpty()) {
                return [
                    'success' => true,
                    'average_rating' => 0,
                    'total_reviews' => 0
                ];
            }

            $averageRating = $reviews->avg('rating');
            $totalReviews = $reviews->count();

            return [
                'success' => true,
                'average_rating' => round($averageRating, 2),
                'total_reviews' => $totalReviews,
                'distribution' => $this->getRatingDistribution($reviews)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الحساب: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get rating distribution
     */
    private function getRatingDistribution($reviews): array
    {
        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];

        foreach ($reviews as $review) {
            $distribution[$review->rating]++;
        }

        return $distribution;
    }

    /**
     * Get product reviews
     */
    public function getProductReviews(int $productId, int $limit = 10): array
    {
        try {
            $reviews = Review::where('product_id', $productId)
                ->where('status', 'approved')
                ->latest()
                ->limit($limit)
                ->get()
                ->toArray();

            return [
                'success' => true,
                'reviews' => $reviews,
                'count' => count($reviews)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الجلب: ' . $e->getMessage(),
                'reviews' => []
            ];
        }
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(int $reviewId): array
    {
        try {
            $review = Review::find($reviewId);
            if (!$review) {
                return [
                    'success' => false,
                    'message' => 'التعليق غير موجود | Review not found'
                ];
            }

            $review->increment('helpful_count');

            return [
                'success' => true,
                'message' => 'شكرا على تقييمك | Thank you for rating'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل التمارك: ' . $e->getMessage()
            ];
        }
    }
}
