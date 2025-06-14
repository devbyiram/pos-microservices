@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('products.index') }}" class="btn btn-primary mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Add Product</h5>

                <div id="success-message" class="alert alert-success d-none">
                    <span id="success-text"></span>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="create-product-form">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Product Name</label>
                                <input type="text" class="form-control" name="name" id="name">
                                <div class="text-danger" id="error-name"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="cost_price">Cost Price</label>
                                <input type="number" step="0.01" class="form-control" name="cost_price" id="cost_price">
                                <div class="text-danger" id="error-cost_price"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="sale_price">Sale Price</label>
                                <input type="number" step="0.01" class="form-control" name="sale_price" id="sale_price">
                                <div class="text-danger" id="error-sale_price"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="stock_quantity">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_quantity" id="stock_quantity">
                                <div class="text-danger" id="error-stock_quantity"></div>
                            </div>

                            <!-- Dropdowns -->
                            @foreach (['store', 'user', 'category', 'brand', 'vendor'] as $field)
                                <div class="form-group mb-3">
                                    <label for="{{ $field }}_id">{{ ucfirst($field) }}</label>
                                    <select class="form-select" id="{{ $field }}_id" name="{{ $field }}_id">
                                        <option value="" disabled selected>Select {{ ucfirst($field) }}</option>
                                    </select>
                                    <div class="text-danger" id="error-{{ $field }}_id"></div>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary">Create Product</button>
                            <button type="reset" class="btn btn-secondary">Cancel</button>
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
    async function loadDropdowns() {
        const endpoints = {
            store: 'stores',
            user: 'users',
            category: 'categories',
            brand: 'brands',
            vendor: 'vendors'
        };

        for (const [key, endpoint] of Object.entries(endpoints)) {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/${endpoint}`, { credentials: 'include' });
                const data = await res.json();
                const select = document.getElementById(`${key}_id`);
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
            } catch (err) {
                console.error(`Failed to load ${key}:`, err);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadDropdowns);

    document.getElementById('create-product-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        ['name', 'cost_price', 'sale_price', 'stock_quantity', 'store_id', 'user_id', 'category_id', 'brand_id', 'vendor_id']
            .forEach(f => document.getElementById(`error-${f}`).innerText = '');

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('http://127.0.0.1:8000/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (response.status === 422) {
                Object.entries(result.errors).forEach(([field, messages]) => {
                    document.getElementById(`error-${field}`).innerText = messages.join(', ');
                });
            } else if (response.ok) {
                document.getElementById('success-text').innerText = 'Product created successfully!';
                document.getElementById('success-message').classList.remove('d-none');
                this.reset();
                setTimeout(() => document.getElementById('success-message').classList.add('d-none'), 5000);
            } else {
                alert('Error: ' + (result.message || 'Something went wrong.'));
            }
        } catch (err) {
            alert('Network/server error');
        }
    });
</script>
@endsection
