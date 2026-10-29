<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ManagementHubController extends Controller
{
    public function catalog(): View
    {
        return view('pages.management.catalog');
    }

    public function inventory(): View
    {
        return view('pages.management.inventory');
    }
}
