<?php

namespace App\Ai\Tools;

use App\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListCategories implements Tool
{
    public function description(): Stringable|string
    {
        return 'List all available product categories. Use this when the user asks about categories or wants to browse products by category.';
    }

    public function handle(Request $request): Stringable|string
    {
        $categories = Category::withCount('products')->get(['id', 'name', 'description']);

        return $categories->toJson();
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}