<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function stocks()
    {
        return view('admin.stocks');
    }

    public function categories()
    {
        return view('admin.categories');
    }

    public function brands()
    {
        return view('admin.brands');
    }

    public function itemTypes()
    {
        return view('admin.item-types');
    }

    public function itemQualityLevels() // NEW METHOD
    {
        return view('admin.item-quality-levels');
    }

    public function makes() // NEW METHOD
    {
        return view('admin.makes');
    }

    public function suppliers()
    {
        return view('admin.suppliers');
    }

    public function warranties()
    {
        return view('admin.warranties');
    }

    public function settings()
    {
        return view('admin.settings');
    }
    public function invoices()
    {
        return view('admin.invoices');
    }

    public function pos()
    {
        return view('admin.pos');
    }

    public function orders()
    {
        return view('admin.orders');
    }

    public function activityLogs()
    {
        return view('admin.activity-logs');
    }

    public function notificationOutbox()
    {
        return view('admin.notification-outbox');
    }

    public function stockMovements()
    {
        return view('admin.stock-movements');
    }

    public function systemHealth()
    {
        return view('admin.system-health');
    }

}
