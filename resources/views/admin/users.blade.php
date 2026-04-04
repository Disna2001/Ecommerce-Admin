@extends('layouts.admin')

@section('header', 'User Management')
@section('breadcrumb', 'Users')

@section('content')
@livewire('admin.user-manager')
@endsection
