<?php

namespace App\Services;

use App\Models\Notification;

/**
 * NotificationService
 *
 * خدمة الإخطارات
 * قدم هذه الخدمة آليات لإرسال والتعامل مع الإخطارات المختلفة
 *
 * This service provides mechanisms for sending and managing various notifications
 * including email, SMS, and in-app notifications for users and vendors
 *
 * @package App\Services
 * @version 1.0.0
 * @author Gulf eCommerce Team
 */
class NotificationService
{
    /**
     * Send email notification
     *
     * إرسال إخطار عبر البريد الإلكترون
     *
     * @param string $recipientEmail المستقبل
     * @param string $subject الموضوع
     * @param string $body جسم الرسالة
     * @param array $data البيانات الإضافية
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendEmailNotification(string $recipientEmail, string $subject, string $body, array $data = []): array
    {
        try {
            Notification::create([
                'user_email' => $recipientEmail,
                'type' => 'email',
                'subject' => $subject,
                'body' => $body,
                'status' => 'sent',
                'metadata' => json_encode($data)
            ]);

            return [
                'success' => true,
                'message' => 'تم إرسال الإخطار بنجاح | Email notification sent successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل إرسال الإخطار: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS notification
     *
     * إرسال إخطار عبر الرسائل النصية
     *
     * @param string $phoneNumber رقم الهاتف
     * @param string $message الرسالة
     * @param array $data البيانات الإضافية
     * @return array ['success' => bool, 'message' => string, 'sms_id' => string]
     */
    public function sendSmsNotification(string $phoneNumber, string $message, array $data = []): array
    {
        try {
            $smsId = 'SMS_' . uniqid();
            
            Notification::create([
                'phone_number' => $phoneNumber,
                'type' => 'sms',
                'body' => $message,
                'status' => 'sent',
                'reference_id' => $smsId,
                'metadata' => json_encode($data)
            ]);

            return [
                'success' => true,
                'message' => 'تم إرسال الرسالة النصية بنجاح | SMS sent successfully',
                'sms_id' => $smsId
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل إرسال الرسالة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send in-app notification
     *
     * إرسال إخطار داخل التطبيق
     *
     * @param int $userId معرّف المستخدم
     * @param string $title العنوان
     * @param string $message الرسالة
     * @param array $data البيانات الإضافية
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendInAppNotification(int $userId, string $title, string $message, array $data = []): array
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => 'in_app',
                'title' => $title,
                'body' => $message,
                'status' => 'unread',
                'metadata' => json_encode($data)
            ]);

            return [
                'success' => true,
                'message' => 'تم إنشاء الإخطار بنجاح | In-app notification created successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل في إنشاء الإخطار: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send push notification
     *
     * إرسال إخطار فوري
     *
     * @param int $userId معرّف المستخدم
     * @param string $title العنوان
     * @param string $message الرسالة
     * @param array $data البيانات الإضافية
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendPushNotification(int $userId, string $title, string $message, array $data = []): array
    {
        try {
            $pushId = 'PUSH_' . uniqid();
            
            Notification::create([
                'user_id' => $userId,
                'type' => 'push',
                'title' => $title,
                'body' => $message,
                'status' => 'sent',
                'reference_id' => $pushId,
                'metadata' => json_encode($data)
            ]);

            return [
                'success' => true,
                'message' => 'تم إرسال الإخطار الفوري بنجاح | Push notification sent successfully',
                'push_id' => $pushId
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل في إرسال الإخطار الفوري: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send order status notification
     *
     * إرسال إخطار تغيير حالة الطلب
     *
     * @param int $orderId معرّف الطلب
     * @param string $status الحالة الجديدة
     * @param int $userId معرّف المستخدم
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendOrderStatusNotification(int $orderId, string $status, int $userId): array
    {
        $statusMessages = [
            'pending' => 'يتم معالجة طلبك | Your order is being processed',
            'processing' => 'قيد المعالجة | Order is being processed',
            'shipped' => 'تم شحن الطلب | Your order has been shipped',
            'delivered' => 'تم التسليم | Your order has been delivered',
            'cancelled' => 'تم إلغاء الطلب | Your order has been cancelled',
            'refunded' => 'تم استرجاع المبلغ | Refund has been processed'
        ];

        $title = 'تحديث حالة الطلب | Order Status Update';
        $message = $statusMessages[$status] ?? 'تحديث على طلبك | Update on your order';

        return $this->sendInAppNotification($userId, $title, $message, [
            'order_id' => $orderId,
            'status' => $status
        ]);
    }

    /**
     * Mark notification as read
     *
     * تحديد الإخطار كمقروء
     *
     * @param int $notificationId معرّف الإخطار
     * @return array ['success' => bool, 'message' => string]
     */
    public function markAsRead(int $notificationId): array
    {
        try {
            $notification = Notification::find($notificationId);
            
            if (!$notification) {
                return [
                    'success' => false,
                    'message' => 'الإخطار غير موجود | Notification not found'
                ];
            }

            $notification->update(['status' => 'read']);

            return [
                'success' => true,
                'message' => 'تم تحديث حالة الإخطار | Notification marked as read'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل تحديث الإخطار: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user notifications
     *
     * الحصول على إخطارات المستخدم
     *
     * @param int $userId معرّف المستخدم
     * @param int $limit الحد الأقصى للنتائج
     * @return array Array of notifications
     */
    public function getUserNotifications(int $userId, int $limit = 20): array
    {
        try {
            $notifications = Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return [
                'success' => true,
                'notifications' => $notifications,
                'count' => count($notifications)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل جلب الإخطارات: ' . $e->getMessage(),
                'notifications' => []
            ];
        }
    }
}
