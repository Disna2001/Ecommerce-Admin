@extends('layouts.admin')

@section('header', 'Category Management')
@section('breadcrumb', 'Categories')

@section('content')
@livewire('settings.category-manager')
@endsection
