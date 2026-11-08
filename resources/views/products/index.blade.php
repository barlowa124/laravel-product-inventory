<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Inventory</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 900px;">

    <h1 class="mb-4">Product Inventory</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 card-title mb-3" id="form-title">Add Product</h2>
            <div id="form-error" class="alert alert-danger d-none" role="alert"></div>
            <form id="product-form" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Product name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity in stock</label>
                        <input type="text" class="form-control" id="quantity" name="quantity" inputmode="numeric" required>
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price per item</label>
                        <input type="text" class="form-control" id="price" name="price" inputmode="decimal" required>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Add Product</button>
                    <button type="button" class="btn btn-outline-secondary d-none" id="cancel-edit-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 card-title mb-3">Submitted Products</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Product name</th>
                            <th>Quantity in stock</th>
                            <th>Price per item</th>
                            <th>Datetime submitted</th>
                            <th>Total value</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="products-body">
                        @forelse ($products as $product)
                            <tr data-id="{{ $product['id'] }}">
                                <td class="product-name">{{ $product['name'] }}</td>
                                <td class="product-quantity">{{ $product['quantity'] }}</td>
                                <td class="product-price">${{ number_format($product['price'], 2) }}</td>
                                <td class="product-submitted">{{ $product['submitted_at'] }}</td>
                                <td class="product-total">${{ number_format($product['total'], 2) }}</td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row"><td colspan="6" class="text-center text-muted py-4">No products submitted yet.</td></tr>
                        @endforelse
                        <tr class="table-secondary fw-bold">
                            <td colspan="4" class="text-end">Sum total</td>
                            <td id="sum-total">${{ number_format($sum, 2) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/products.js') }}"></script>
</body>
</html>
