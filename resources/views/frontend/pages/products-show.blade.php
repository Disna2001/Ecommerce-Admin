@extends('layouts.shop')
@section('title', $product->name)
@section('content')
    <livewire:shop.product-detail :product="$product" />
@endsection