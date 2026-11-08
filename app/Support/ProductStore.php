<?php

namespace App\Support;

use Illuminate\Support\Str;

class ProductStore
{
    private string $path;

    public function __construct()
    {
        $this->path = storage_path('app/products.json');
    }

    public function all(): array
    {
        $items = $this->read();
        usort($items, fn (array $a, array $b) => strcmp($a['submitted_at'], $b['submitted_at']));

        return $items;
    }

    public function create(array $data): array
    {
        $items = $this->read();
        $item = $this->fields($data) + [
            'id' => (string) Str::uuid(),
            'submitted_at' => now()->format('Y-m-d H:i:s'),
        ];
        $items[] = $item;
        $this->write($items);

        return $item;
    }

    public function update(string $id, array $data): ?array
    {
        $items = $this->read();
        foreach ($items as $i => $item) {
            if ($item['id'] === $id) {
                $items[$i] = array_merge($item, $this->fields($data));
                $this->write($items);

                return $items[$i];
            }
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $items = $this->read();
        $kept = array_values(array_filter($items, fn (array $item) => $item['id'] !== $id));
        if (count($kept) === count($items)) {
            return false;
        }
        $this->write($kept);

        return true;
    }

    public function sum(): float
    {
        return array_sum(array_column($this->all(), 'total'));
    }

    private function fields(array $data): array
    {
        $quantity = (int) $data['quantity'];
        $price = (float) $data['price'];

        return [
            'name' => trim($data['name']),
            'quantity' => $quantity,
            'price' => $price,
            'total' => round($quantity * $price, 2),
        ];
    }

    private function read(): array
    {
        if (! is_file($this->path)) {
            return [];
        }
        $json = file_get_contents($this->path);
        $items = json_decode($json === false ? '' : $json, true);

        return is_array($items) ? $items : [];
    }

    private function write(array $items): void
    {
        file_put_contents(
            $this->path,
            json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }
}
