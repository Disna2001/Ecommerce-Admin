@extends('layouts.admin')

@section('header', 'Stock Movements')
@section('breadcrumb', 'Inventory Ledger')

@section('content')
    <livewire:admin.stock-movement-log-manager />
@endsection
