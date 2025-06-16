@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('product-variants.index') }}" class="btn btn-primary mb-4">Back</a>
                    <h5 class="card-title fw-semibold mb-4">Add Product Variant</h5>

                    <div id="success-message" class="alert alert-success d-none">
                        <span id="success-text"></span>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="create-variant-form">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="product_id">Product</label>
                                    <select class="form-select" id="product_id" name="product_id">
                                        <option value="" disabled selected>Select Product</option>
                                    </select>
                                    <div class="text-danger" id="error-product_id"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="sku">SKU</label>
                                    <input type="text" class="form-control" name="sku" id="sku">
                                    <div class="text-danger" id="error-sku"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="price">Price</label>
                                    <input type="number" step="0.01" class="form-control" name="price" id="price">
                                    <div class="text-danger" id="error-price"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="stock_quantity">Stock Quantity</label>
                                    <input type="number" class="form-control" name="stock_quantity" id="stock_quantity">
                                    <div class="text-danger" id="error-stock_quantity"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="tax">Tax</label>
                                    <input type="number" class="form-control" name="tax" id="tax">
                                    <div class="text-danger" id="error-tax"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="tax_type">Tax Type</label>
                                    <select class="form-select" name="tax_type" id="tax_type">
                                        <option value="" disabled selected>Select Tax Type</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed</option>
                                    </select>
                                    <div class="text-danger" id="error-tax_type"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="discount">Discount</label>
                                    <input type="number" class="form-control" name="discount" id="discount">
                                    <div class="text-danger" id="error-discount"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="discount_type">Discount Type</label>
                                    <select class="form-select" name="discount_type" id="discount_type">
                                        <option value="" disabled selected>Select Discount Type</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed</option>
                                    </select>
                                    <div class="text-danger" id="error-discount_type"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Create Variant</button>
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
        async function loadProducts() {
            const res = await fetch('http://127.0.0.1:8000/api/products', { credentials: 'include' });
            const data = await res.json();
            const select = document.getElementById('product_id');
            data.forEach(p => {
                select.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
        }

        document.addEventListener('DOMContentLoaded', loadProducts);

        document.getElementById('create-variant-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            [
                'product_id', 'sku', 'price', 'stock_quantity',
                'tax', 'tax_type', 'discount', 'discount_type'
            ].forEach(f => document.getElementById(`error-${f}`).innerText = '');

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('http://127.0.0.1:8000/api/product-variants', {
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
                    document.getElementById('success-text').innerText = 'Variant created successfully!';
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
