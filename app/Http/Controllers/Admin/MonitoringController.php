<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Router;
use Inertia\Inertia;
use Inertia\Response;

class MonitoringController extends Controller
{
    public function index(): Response
    {
        $routers = Router::with(['metrics' => fn ($q) => $q->latest()->limit(1)])->get();

        return Inertia::render('Admin/Monitoring/Index', ['routers' => $routers]);
    }
}
