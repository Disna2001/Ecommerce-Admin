@extends('layouts.admin')

@section('header', 'Activity Logs')
@section('breadcrumb', 'Audit Trail')

@section('content')
    <livewire:admin.admin-activity-log-manager />
@endsection
