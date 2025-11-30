@extends('layouts.shop')

@section('content')
<div class="products-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container py-4">
        <!-- Page Title -->
        <h1 class="page-title mb-4">{{ __('messages.products') }}</h1>
        
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-md-3">
                <div class="filters-sidebar">
                    <h5>{{ __('messages.filters') }}</h5>
                    
                    <!-- Category Filter -->
                    <div class="filter-group mb-3">
                        <h6>{{ __('messages.categories') }}</h6>
                        <div class="form-check">
                            @foreach($categories as $category)
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input category-filter" 
                                           value="{{ $category->id }}">
                                    {{ $category->name_ar ?? $category->name_en }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="filter-group mb-3">
                        <h6>{{ __('messages.price_range') }}</h6>
                        <input type="range" class="form-range" id="priceRange" min="0" max="10000">
                        <p><span id="priceDisplay">0</span> - <span id="priceMax">10000</span></p>
                    </div>
                    
                    <!-- Rating Filter -->
                    <div class="filter-group">
                        <h6>{{ __('messages.rating') }}</h6>
                        @for($i = 5; $i >= 1; $i--)
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input rating-filter" value="{{ $i }}">
                                {{ str_repeat('★', $i) }} {{ $i }}+
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-md-9">
                <!-- Sort Options -->
                <div class="sort-options mb-4 d-flex justify-content-between align-items-center">
                    <p>{{ __('messages.showing') }} {{ count($products) }} {{ __('messages.products') }}</p>
                    <select class="form-select sort-select" id="sortBy">
                        <option value="newest">{{ __('messages.newest') }}</option>
                        <option value="price_asc">{{ __('messages.price_low_to_high') }}</option>
                        <option value="price_desc">{{ __('messages.price_high_to_low') }}</option>
                        <option value="popular">{{ __('messages.most_popular') }}</option>
                    </select>
                </div>
                
                <!-- Products Grid -->
                <div class="products-grid row g-4">
                    @forelse($products as $product)
                        <div class="col-md-6 col-lg-4">
                            <div class="product-card h-100">
                                <div class="product-image position-relative">
                                    <img src="{{ asset('storage/' . $product->main_image) }}" 
                                         alt="{{ $product->name_ar ?? $product->name_en }}" class="img-fluid">
                                    @if($product->discount > 0)
                                        <span class="discount-badge">-{{ $product->discount }}%</span>
                                    @endif
                                    <button class="wishlist-btn btn-icon" data-product-id="{{ $product->id }}">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                                
                                <div class="product-body p-3">
                                    <h6 class="product-name">{{ $product->name_ar ?? $product->name_en }}</h6>
                                    
                                    <div class="product-rating mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $product->average_rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                        <span class="rating-count">({{ $product->reviews_count }})</span>
                                    </div>
                                    
                                    <div class="product-price mb-3">
                                        <span class="current-price">{{ $product->current_price }} {{ auth()->user()?->currency ?? 'SAR' }}</span>
                                        @if($product->original_price > $product->current_price)
                                            <span class="original-price">{{ $product->original_price }} {{ auth()->user()?->currency ?? 'SAR' }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="product-actions">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-primary w-100">
                                            {{ __('messages.view_details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">{{ __('messages.no_products_found') }}</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
