<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ProductController - معالج المنتجات
 * 
 * يدير عمليات البحث عن المنتجات وإنشاؤها وتحديثها وحذفها
 * Handles product search, creation, update, and deletion operations
 */
class ProductController extends Controller
{
    /**
     * ProductService instance
     * @var ProductService
     */
    protected $productService;

    /**
     * Constructor - البناء
     * 
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('auth:api', ['except' => ['index', 'show', 'search']]);
    }

    /**
     * Get all products with pagination - الحصول على لائحة المنتجات
     * 
     * GET /api/products
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);
            $vendor_id = $request->input('vendor_id');
            $category = $request->input('category');

            $query = Product::query();
            
            if ($vendor_id) {
                $query->where('vendor_id', $vendor_id);
            }
            
            if ($category) {
                $query->where('category', $category);
            }
            
            $products = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'لائحة المنتجات | Products list',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التلامس | Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products - البحث عن المنتجات
     * 
     * GET /api/products/search
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q');
            $category = $request->input('category');
            $min_price = $request->input('min_price');
            $max_price = $request->input('max_price');

            $products = $this->productService->search($query, $category, $min_price, $max_price);

            return response()->json([
                'success' => true,
                'message' => 'نتائج البحث | Search results',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل البحث | Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single product - الحصول على منتج واحد
     * 
     * GET /api/products/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'تفاصيل المنتج | Product details',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود | Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new product - إنشاء منتج جديد
     * 
     * POST /api/products
     * 
     * @param CreateProductRequest $request
     * @return JsonResponse
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            
            if (!$user || ($user->role !== 'vendor' && $user->role !== 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            $product = $this->productService->create(
                $request->all(),
                $user->role === 'vendor' ? $user->id : $request->input('vendor_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المنتج بنجاح | Product created successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء المنتج | Product creation failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update product - تحديث المنتج
     * 
     * PUT /api/products/{id}
     * 
     * @param int $id
     * @param UpdateProductRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateProductRequest $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $user = auth('api')->user();

            if ($user->role === 'vendor' && $product->vendor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            $product = $this->productService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المنتج بنجاح | Product updated successfully',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث المنتج | Product update failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete product - حذف المنتج
     * 
     * DELETE /api/products/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $user = auth('api')->user();

            if ($user->role === 'vendor' && $product->vendor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مرخص | Unauthorized'
                ], 403);
            }

            $this->productService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح | Product deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف المنتج | Product deletion failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get vendor's products - منتجات البائع
     * 
     * GET /api/vendors/{vendor_id}/products
     * 
     * @param int $vendor_id
     * @return JsonResponse
     */
    public function vendorProducts(int $vendor_id): JsonResponse
    {
        try {
            $products = Product::where('vendor_id', $vendor_id)->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'منتجات البائع | Vendor products',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التلامس | Failed to fetch vendor products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
