<?php

namespace App\Ai\Tools;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProductStatistics implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get total count of products and general shop statistics. Use this when the user asks how many products, items, or total inventory exists.';
    }

    public function handle(Request $request): Stringable|string
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        return json_encode([
            'total_products' => $totalProducts,
            'total_categories' => $totalCategories,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return []; // Không cần tham số đầu vào
    }
}