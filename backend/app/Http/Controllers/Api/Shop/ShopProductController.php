<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use Illuminate\Http\JsonResponse;

class ShopProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = ShopProduct::query()
            ->with([
                'category',
                'variants' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = ShopProduct::query()
            ->with([
                'category',
                'variants' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Proizvod nije pronađen.',
            ], 404);
        }

        return response()->json([
            'data' => $product,
        ]);
    }
}
