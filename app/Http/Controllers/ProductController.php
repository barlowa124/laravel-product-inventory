<?php

namespace App\Http\Controllers;

use App\Support\ProductStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(ProductStore $store): View
    {
        return view('products.index', [
            'products' => $store->all(),
            'sum' => $store->sum(),
        ]);
    }

    public function store(Request $request, ProductStore $store): JsonResponse
    {
        $data = $request->validate($this->rules());
        $store->create($data);

        return $this->payload($store);
    }

    public function update(Request $request, string $id, ProductStore $store): JsonResponse
    {
        $data = $request->validate($this->rules());
        if ($store->update($id, $data) === null) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->payload($store);
    }

    public function destroy(string $id, ProductStore $store): JsonResponse
    {
        if (! $store->delete($id)) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->payload($store);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    private function payload(ProductStore $store): JsonResponse
    {
        return response()->json([
            'products' => $store->all(),
            'sum' => $store->sum(),
        ]);
    }
}
