@extends('layouts.shop')

@section('content')
<!-- Hero Section -->
<div class="hero-section" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container">
        <div class="hero-content">
            <h1>{{ __('messages.welcome_to_store') }}</h1>
            <p>{{ __('messages.discover_products') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                {{ __('messages.shop_now') }}
            </a>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="featured-section py-5" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container">
        <h2 class="section-title">{{ __('messages.featured_products') }}</h2>
        <div class="products-grid">
            @foreach($featured_products as $product)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name_ar ?? $product->name_en }}">
                    </div>
                    <div class="product-info">
                        <h3>{{ $product->name_ar ?? $product->name_en }}</h3>
                        <p class="product-description">{{ Str::limit($product->description_ar ?? $product->description_en, 100) }}</p>
                        <div class="product-footer">
                            <span class="price">
                                {{ $product->price }} {{ auth()->user()?->currency ?? 'SAR' }}
                            </span>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline">
                                {{ __('messages.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="categories-section py-5 bg-light" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container">
        <h2 class="section-title">{{ __('messages.shop_by_category') }}</h2>
        <div class="categories-grid">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card">
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name_ar ?? $category->name_en }}">
                    <h3>{{ $category->name_ar ?? $category->name_en }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
