<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\API\ApiBaseController;
use Inertia\Inertia;

class DashboardController extends ApiBaseController
{
    public function index() {
        return Inertia::render('Dashboard/Index');
    }
}
