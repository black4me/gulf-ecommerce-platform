<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * AuthController - معالج المصادقة والتسجيل
 * 
 * يدير عمليات التسجيل والدخول والخروج وتحديث المحتوى
 * Handles user registration, login, logout and profile management
 */
class AuthController extends Controller
{
    /**
     * AuthService instance
     * @var AuthService
     */
    protected $authService;

    /**
     * Constructor - البناء
     * 
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    /**
     * Register new user - تسجيل مستخدم جديد
     * 
     * POST /api/auth/register
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
                $request->input('role', 'customer')
            );

            $token = auth('api')->login($user);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحساب بنجاح | User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التسجيل | Registration failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * User login - دخول المستخدم
     * 
     * POST /api/auth/login
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if ($credentials->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق | Validation failed',
                    'errors' => $credentials->errors()
                ], 422);
            }

            $user = User::where('email', $request->input('email'))->first();
            
            if (!$user || !\Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات اعتماد غير صحيحة | Invalid credentials'
                ], 401);
            }

            $token = auth('api')->login($user);

            return response()->json([
                'success' => true,
                'message' => 'تم الدخول بنجاح | Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الدخول | Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user profile - الحصول على ملف المستخدم الحالي
     * 
     * GET /api/auth/me
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصرح | User not authenticated'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'ملف المستخدم | User profile',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الحصول على الملف | Failed to fetch profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile - تحديث ملف المستخدم
     * 
     * PUT /api/auth/profile
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . auth('api')->id(),
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق | Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $this->authService->updateProfile(
                auth('api')->user(),
                $request->only(['name', 'email', 'phone', 'address'])
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الملف بنجاح | Profile updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث الملف | Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User logout - خروج المستخدم
     * 
     * POST /api/auth/logout
     * 
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح | Logout successful'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تسجيل الخروج | Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh authentication token - تحديث رمز المصادقة
     * 
     * POST /api/auth/refresh
     * 
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = auth('api')->refresh();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الرمز | Token refreshed',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث الرمز | Token refresh failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}
