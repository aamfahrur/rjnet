<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(): Response
    {
        $logs = \Spatie\Activitylog\Models\Activity::with('causer')->latest()->paginate(50);
        return Inertia::render('Admin/ActivityLogs/Index', ['logs' => $logs]);
    }
}
