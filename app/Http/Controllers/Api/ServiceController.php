<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;

class ServiceController extends Controller
{
    /** Liste tous les services groupés par catégorie */
    public function index()
    {
        $categories = Category::with(['services' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        return response()->json($categories);
    }

    /** Détail d'un service */
    public function show(Service $service)
    {
        $service->load('category');
        return response()->json($service);
    }
}
