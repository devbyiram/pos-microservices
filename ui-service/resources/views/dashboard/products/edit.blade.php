@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('products.index') }}" class="btn btn-primary mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Edit Product</h5>

                <div class="card">
                    <div class="card-body">
                        <form id="edit-product-form">
                            @csrf
                            <input type="hidden" id="product_id" value="{{ $product_id }}">

                            <div class="form-group mb-3">
                                <label for="name">Product Name</label>
                                <input type="text" class="form-control" name="name" id="name">
                                <div class="text-danger" id="error-name"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="cost_price">Cost Price</label>
                                <input type="number" class="form-control" name="cost_price" id="cost_price">
                                <div class="text-danger" id="error-cost_price"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="sale_price">Sale Price</label>
                                <input type="number" class="form-control" name="sale_price" id="sale_price">
                                <div class="text-danger" id="error-sale_price"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="stock_quantity">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_quantity" id="stock_quantity">
                                <div class="text-danger" id="error-stock_quantity"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="store_id">Store</label>
                                <select class="form-select" name="store_id" id="store_id">
                                    <option value="" disabled selected>Select Store</option>
                                </select>
                                <div class="text-danger" id="error-store_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="user_id">User</label>
                                <select class="form-select" name="user_id" id="user_id">
                                    <option value="" disabled selected>Select User</option>
                                </select>
                                <div class="text-danger" id="error-user_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="category_id">Category</label>
                                <select class="form-select" name="category_id" id="category_id">
                                    <option value="" disabled selected>Select Category</option>
                                </select>
                                <div class="text-danger" id="error-category_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="brand_id">Brand</label>
                                <select class="form-select" name="brand_id" id="brand_id">
                                    <option value="" disabled selected>Select Brand</option>
                                </select>
                                <div class="text-danger" id="error-brand_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="vendor_id">Vendor</label>
                                <select class="form-select" name="vendor_id" id="vendor_id">
                                    <option value="" disabled selected>Select Vendor</option>
                                </select>
                                <div class="text-danger" id="error-vendor_id"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Product</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@parent
<script>
    const productId = document.getElementById('product_id').value;

    async function populateDropdown(url, elementId, selectedId = null) {
        const res = await fetch(url, { credentials: 'include' });
        const data = await res.json();
        const select = document.getElementById(elementId);
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.text = item.name;
            if (selectedId && item.id == selectedId) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    async function loadProductDetails() {
        try {
            const res = await fetch(`http://127.0.0.1:8000/api/products/${productId}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const product = await res.json();

            document.getElementById('name').value = product.name;
            document.getElementById('cost_price').value = product.cost_price;
            document.getElementById('sale_price').value = product.sale_price;
            document.getElementById('stock_quantity').value = product.stock_quantity;

            await Promise.all([
                populateDropdown('http://127.0.0.1:8000/api/stores', 'store_id', product.store_id),
                populateDropdown('http://127.0.0.1:8000/api/users', 'user_id', product.user_id),
                populateDropdown('http://127.0.0.1:8000/api/categories', 'category_id', product.category_id),
                populateDropdown('http://127.0.0.1:8000/api/brands', 'brand_id', product.brand_id),
                populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id', product.vendor_id)
            ]);
        } catch (err) {
            console.error('Failed to load product:', err);
            alert('Failed to load product data.');
        }
    }

    loadProductDetails();

    document.getElementById('edit-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        ['name', 'cost_price', 'sale_price', 'stock_quantity', 'store_id', 'user_id', 'category_id', 'brand_id', 'vendor_id'].forEach(field => {
            document.getElementById(`error-${field}`).innerText = '';
        });

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/products/${productId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.errors) {
                    Object.entries(result.errors).forEach(([field, messages]) => {
                        document.getElementById(`error-${field}`).innerText = messages[0];
                    });
                } else {
                    alert('Error: ' + result.message);
                }
            } else {
                window.location.href = '/products';
            }
        } catch (err) {
            console.error('Update failed:', err);
            alert('Server or network error occurred.');
        }
    });
</script>
@endsection
