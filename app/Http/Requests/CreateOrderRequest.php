<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CreateOrderRequest - طلب إنشاء طلب
 * 
 * يتحقق من بيانات إنشاء الطلب
 * Validates order creation request data
 */
class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized - التحقق من الترخيص
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules - قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer'
        ];
    }
}
