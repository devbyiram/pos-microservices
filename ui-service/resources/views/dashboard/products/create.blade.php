@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('products.index') }}" class="btn btn-primary mb-4">Back</a>
                    <h5 class="card-title fw-semibold mb-4">Create Product</h5>

                    <div class="card">
                        <div class="card-body">
                            <form id="create-product-form" enctype="multipart/form-data" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">Product Information</h6>

                                    {{-- Existing Fields --}}
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
                                            <option value="" disabled selected>Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="text-danger" id="error-status"></div>
                                    </div>
                                </div>
                                    <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">Images</h6>
                                    <input type="file" class="form-control" name="images[]" id="images" multiple>
                                    <div class="text-danger" id="error-images"></div>
                                </div>

                                    {{-- Product Type --}}
                                    <div class="mb-4">
                                        <h6 class="fw-semibold mb-3">Pricing & Stocks</h6>

                                        <div class="mb-3">
                                            <label class="form-label me-3">Product Type <span
                                                    class="text-danger">*</span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="product_type"
                                                    id="product_type_single" value="single" checked>
                                                <label class="form-check-label" for="product_type_single">Single
                                                    Product</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="product_type"
                                                    id="product_type_variable" value="variable">
                                                <label class="form-check-label" for="product_type_variable">Variable
                                                    Product</label>
                                            </div>
                                            <div class="text-danger" id="error-product_type"></div>
                                        </div>

                                        <div id="single-product-fields">
                                            <div class="row g-3">
                                                <div class="col-lg-4">
                                                    <label for="quantity">Quantity <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="quantity"
                                                        id="quantity" min="0">
                                                    <div class="text-danger" id="error-quantity"></div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="price" >Price <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="price" id="price" min="0">
                                                    <div class="text-danger" id="error-price"></div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="tax" >Tax (%)</label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="tax" id="tax" min="0">
                                                    <div class="text-danger" id="error-tax"></div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="tax_type" >Tax Type</label>
                                                    <select class="form-select" name="tax_type" id="tax_type">
                                                        <option value="" disabled selected>Select</option>
                                                        <option value="fixed">Fixed</option>
                                                        <option value="percentage">Percentage</option>
                                                    </select>
                                                    <div class="text-danger" id="error-tax_type"></div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="discount_type">Discount
                                                        Type</label>
                                                    <select class="form-select" name="discount_type" id="discount_type">
                                                        <option value="" disabled selected>Select</option>
                                                        <option value="percentage">Percentage</option>
                                                        <option value="fixed">Fixed</option>
                                                    </select>
                                                    <div class="text-danger" id="error-discount_type"></div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="discount_value">Discount
                                                        Value</label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="discount_value" id="discount_value" min="0">
                                                    <div class="text-danger" id="error-discount_value"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <button type="submit" class="btn btn-primary">Create Product</button>
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
        async function populateDropdown(url, elementId) {
            const res = await fetch(url, {
                credentials: 'include'
            });
            const data = await res.json();
            const select = document.getElementById(elementId);
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.text = item.name;
                select.appendChild(option);
            });
        }

        async function loadDropdowns() {
            await Promise.all([
                populateDropdown('http://127.0.0.1:8000/api/stores', 'store_id'),
                populateDropdown('http://127.0.0.1:8000/api/users', 'user_id'),
                populateDropdown('http://127.0.0.1:8000/api/categories', 'category_id'),
                populateDropdown('http://127.0.0.1:8000/api/brands', 'brand_id'),
                populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id')
            ]);
        }

        loadDropdowns();

        document.querySelectorAll('input[name=\"product_type\"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.getElementById('single-product-fields').style.display =
                    document.getElementById('product_type_single').checked ? 'block' : 'none';
            });
        });

        document.getElementById('create-product-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const images = document.getElementById('images').files;
            for (let i = 0; i < images.length; i++) {
                formData.append('images[]', images[i]);
            }

            try {
                const response = await fetch('http://127.0.0.1:8000/api/products', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const errorEl = document.getElementById(`error-${field}`);
                            if (errorEl) errorEl.innerText = messages[0];
                        });
                    } else {
                        alert('Error: ' + result.message);
                    }
                } else {
                    window.location.href = '/products';
                }
            } catch (err) {
                console.error('Create failed:', err);
                alert('Network or server error.');
            }
        });
    </script>
@endsection
