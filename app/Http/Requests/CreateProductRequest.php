<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CreateProductRequest - طلب إنشاء المنتج
 * 
 * يتحقق من بيانات إنشاء المنتج
 * Validates product creation request data
 */
class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request - التحقق من الترخيص
     */
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['vendor', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request - قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0',
            'sku' => 'sometimes|string|unique:products,sku',
            'category' => 'sometimes|string|max:100',
            'images' => 'sometimes|array|max:5',
            'images.*' => 'url',
            'status' => 'sometimes|in:active,inactive,draft'
        ];
    }

    /**
     * Get the error messages for the defined validation rules - رسالل الأخطاء
     */
    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب | Product name is required',
            'price.required' => 'السعر مطلوب | Product price is required',
            'quantity.required' => 'الكمية مطلوبة | Product quantity is required',
            'sku.unique' => 'يوجد SKU واحد بالفعل | SKU already exists'
        ];
    }
}
