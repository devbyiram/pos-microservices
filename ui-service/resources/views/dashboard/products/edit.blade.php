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
                            <form id="edit-product-form" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="product_id" value="{{ $product_id }}">

                                <div class="form-group mb-3">
                                    <label for="name">Product Name</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                    <div class="text-danger" id="error-name"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="item_code">Item Code</label>
                                    <input type="text" class="form-control" name="item_code" id="item_code">
                                    <div class="text-danger" id="error-item_code"></div>
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

                                <div class="form-group mb-3">
                                    <label for="status">Status</label>
                                    <select class="form-select" name="status" id="status">
                                        <option value="" disabled selected>Select status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="text-danger" id="error-status"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="images">Product Images</label>
                                    <input type="file" class="form-control" name="images[]" id="images" multiple>
                                    <div class="text-danger" id="error-images"></div>
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
        const submitButton = document.querySelector('#edit-product-form button[type="submit"]');
        submitButton.disabled = true; // Disable initially

        async function populateDropdown(url, elementId, selectedId = null) {
            const res = await fetch(url, {
                credentials: 'include'
            });
            const data = await res.json();
            const select = document.getElementById(elementId);
            select.innerHTML = ''; // clear existing

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
                document.getElementById('item_code').value = product.item_code || '';
                document.getElementById('status').value = product.status;

                await Promise.all([
                    populateDropdown('http://127.0.0.1:8000/api/stores', 'store_id', product.store_id),
                    populateDropdown('http://127.0.0.1:8000/api/users', 'user_id', product.user_id),
                    populateDropdown('http://127.0.0.1:8000/api/categories', 'category_id', product
                    .category_id),
                    populateDropdown('http://127.0.0.1:8000/api/brands', 'brand_id', product.brand_id),
                    populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id', product.vendor_id)
                ]);

                submitButton.disabled = false; // ✅ Enable after everything is loaded
            } catch (err) {
                console.error('Failed to load product:', err);
                alert('Failed to load product data.');
            }
        }

        loadProductDetails();

        document.getElementById('edit-product-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            ['name', 'item_code', 'store_id', 'user_id', 'category_id', 'brand_id', 'vendor_id', 'status',
                'images'
            ]
            .forEach(field => {
                const errorDiv = document.getElementById(`error-${field}`);
                if (errorDiv) errorDiv.innerText = '';
            });

            const formData = new FormData();
            const fields = ['name', 'item_code', 'store_id', 'user_id', 'category_id', 'brand_id', 'vendor_id',
                'status'
            ];

            fields.forEach(id => {
                const value = document.getElementById(id)?.value || '';
                formData.append(id, value);
            });

            const images = document.getElementById('images').files;
            for (let i = 0; i < images.length; i++) {
                if (images[i]) {
                    formData.append(`images[]`, images[i]);
                }
            }

            formData.append('_method', 'PUT');

            // Log formData to debug
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/products/${productId}`, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include',
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const errorDiv = document.getElementById(`error-${field}`);
                            if (errorDiv) errorDiv.innerText = messages[0];
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
