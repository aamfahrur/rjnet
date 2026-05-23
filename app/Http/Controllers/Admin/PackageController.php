<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternetPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    public function index(): Response
    {
        $packages = InternetPackage::latest()->paginate(25);
        return Inertia::render('Admin/Packages/Index', ['packages' => $packages]);
    }

    public function store(Request $request): RedirectResponse
    {
        InternetPackage::create($request->validate([
            'name'               => 'required|string|max:100',
            'code'               => 'required|string|max:50|unique:internet_packages',
            'price'              => 'required|integer',
            'download_speed_bps' => 'required|integer',
            'upload_speed_bps'   => 'required|integer',
            'is_active'          => 'boolean',
        ]));

        return back()->with('success', 'Paket berhasil ditambahkan.');
    }

    public function update(Request $request, InternetPackage $package): RedirectResponse
    {
        $package->update($request->validate([
            'name'               => 'string|max:100',
            'price'              => 'integer',
            'download_speed_bps' => 'integer',
            'upload_speed_bps'   => 'integer',
            'is_active'          => 'boolean',
        ]));

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(InternetPackage $package): RedirectResponse
    {
        $package->delete();
        return back()->with('success', 'Paket berhasil dihapus.');
    }
}
