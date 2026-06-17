<?php
namespace App\Ai\Tools;
use App\Models\Product;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchProducts implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search for products by name. Use this when the user asks about a product or wants to find products.';
    }

    public function handle(Request $request): Stringable|string
    {
        $products = Product::with('category')
            ->where('name', 'like', '%' .  $request['query']  . '%')
            ->limit(10)
            ->get(['id', 'name', 'price', 'stock', 'category_id']);

        if ($products->isEmpty()) {
            return 'No products found matching "' . $request['query'] . '".';
        }

        return $products->toJson();
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string('The search keyword to find products by name.')->required(),
        ];
    }
}