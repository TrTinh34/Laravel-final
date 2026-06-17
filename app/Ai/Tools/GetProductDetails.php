<?php

namespace App\Ai\Tools;

use App\Models\Product;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProductDetails implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get detailed information about a specific product by its ID. Use this when the user asks for details about a particular product.';
    }

    public function handle(Request $request): Stringable|string
    {
        $product = Product::with('category')->find($request['product_id']);

        if (! $product) {
            return 'Product not found.';
        }

        return json_encode([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'content' => $product->content,
            'category' => $product->category?->name,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'product_id' => $schema->integer('The ID of the product to retrieve.')->required(),
        ];
    }
}