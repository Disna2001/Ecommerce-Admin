@extends('layouts.admin')

@section('header', 'Role Management')
@section('breadcrumb', 'Roles')

@section('content')
@livewire('settings.role-manager')
@endsection
