<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateProductRequest - طلب تحديث المنتج
 * 
 * يتحقق من بيانات تحديث المنتج
 * Validates product update request data
 */
class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     */
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['vendor', 'admin']);
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'price' => 'sometimes|numeric|min:0.01',
            'quantity' => 'sometimes|integer|min:0',
            'category' => 'sometimes|string|max:100',
            'images' => 'sometimes|array|max:5',
            'images.*' => 'url',
            'status' => 'sometimes|in:active,inactive,draft'
        ];
    }
}
