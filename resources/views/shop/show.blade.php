@extends('layouts.shop')

@section('content')
<div class="product-detail-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container py-4">
        <nav class="breadcrumb mb-4">
            <span><a href="{{ route('home') }}">{{ __('messages.home') }}</a></span>
            <span> / </span>
            <span><a href="{{ route('products.index') }}">{{ __('messages.products') }}</a></span>
            <span> / </span>
            <span>{{ $product->name_ar ?? $product->name_en }}</span>
        </nav>
        
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-5">
                <div class="product-images">
                    <div class="main-image">
                        <img id="mainImage" src="{{ asset('storage/' . $product->main_image) }}" 
                             alt="{{ $product->name_ar ?? $product->name_en }}" class="img-fluid">
                    </div>
                    <div class="thumbnail-images mt-3 d-flex gap-2">
                        <img src="{{ asset('storage/' . $product->main_image) }}" class="img-thumbnail thumbnail-image" 
                             onclick="changeImage(this.src)" style="cursor:pointer;">
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" class="img-thumbnail thumbnail-image" 
                                 onclick="changeImage(this.src)" style="cursor:pointer;">
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="col-md-7">
                <div class="product-details">
                    <h1>{{ $product->name_ar ?? $product->name_en }}</h1>
                    
                    <div class="product-rating mb-3">
                        <span class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $product->average_rating ? 'filled' : '' }}">★</span>
                            @endfor
                        </span>
                        <span class="rating-count">{{ $product->reviews_count }} {{ __('messages.reviews') }}</span>
                    </div>
                    
                    <div class="product-pricing mb-3">
                        <h2 class="price">{{ $product->current_price }} {{ auth()->user()?->currency ?? 'SAR' }}</h2>
                        @if($product->original_price > $product->current_price)
                            <span class="original-price">{{ $product->original_price }}</span>
                            <span class="discount">-{{ round(($product->original_price - $product->current_price) / $product->original_price * 100) }}%</span>
                        @endif
                    </div>
                    
                    <p class="description mb-4">{{ $product->description_ar ?? $product->description_en }}</p>
                    
                    <!-- Specifications -->
                    @if($product->specifications)
                        <div class="specifications mb-4">
                            <h5>{{ __('messages.specifications') }}</h5>
                            <ul>
                                @foreach(json_decode($product->specifications) as $spec => $value)
                                    <li><strong>{{ $spec }}:</strong> {{ $value }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Add to Cart -->
                    <div class="add-to-cart-section mb-4">
                        <div class="quantity-selector mb-3">
                            <label>{{ __('messages.quantity') }}:</label>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control">
                        </div>
                        
                        <button class="btn btn-primary btn-lg w-100 add-to-cart-btn" data-product-id="{{ $product->id }}">
                            <i class="fas fa-shopping-cart"></i> {{ __('messages.add_to_cart') }}
                        </button>
                        
                        <button class="btn btn-outline-primary btn-lg w-100 mt-2 wishlist-btn" data-product-id="{{ $product->id }}">
                            <i class="fas fa-heart"></i> {{ __('messages.add_to_wishlist') }}
                        </button>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="stock-status mb-4">
                        @if($product->stock > 0)
                            <span class="badge bg-success">{{ __('messages.in_stock') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('messages.out_of_stock') }}</span>
                        @endif
                    </div>
                    
                    <!-- Vendor Info -->
                    <div class="vendor-info p-3 bg-light">
                        <h6>{{ __('messages.seller') }}</h6>
                        <p><strong>{{ $product->vendor->name }}</strong></p>
                        <p class="text-muted">{{ $product->vendor->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
