<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteManagementController extends Controller
{
    public function index()
    {
        return view('admin.site-management.index');
    }

    public function appearance()
    {
        return view('admin.site-management.appearance');
    }

    public function banners()
    {
        return view('admin.site-management.banners');
    }

    public function discounts()
    {
        return view('admin.site-management.discounts');
    }

    public function displayItems()
    {
        return view('admin.site-management.display-items');
    }

        public function reviews()
    {
        return view('admin.site-management.reviews');
    }
}