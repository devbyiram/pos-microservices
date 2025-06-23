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

                                <!-- ================= Product Information ================= -->
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">Product Information</h6>

                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label for="name">Product Name</label>
                                            <input type="text" class="form-control" name="name" id="name">
                                            <div class="text-danger" id="error-name"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="item_code">Item Code</label>
                                            <input type="text" class="form-control" name="item_code" id="item_code">
                                            <div class="text-danger" id="error-item_code"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="store_id">Store</label>
                                            <select class="form-select" name="store_id" id="store_id"></select>
                                            <div class="text-danger" id="error-store_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="category_id">Category</label>
                                            <select class="form-select" name="category_id" id="category_id"></select>
                                            <div class="text-danger" id="error-category_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="brand_id">Brand</label>
                                            <select class="form-select" name="brand_id" id="brand_id"></select>
                                            <div class="text-danger" id="error-brand_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="vendor_id">Vendor</label>
                                            <select class="form-select" name="vendor_id" id="vendor_id"></select>
                                            <div class="text-danger" id="error-vendor_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="status">Status</label>
                                            <select class="form-select" name="status" id="status">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                            <div class="text-danger" id="error-status"></div>
                                        </div>
                                    </div>
                                </div>
<div class="mb-4">
    <h6 class="fw-semibold mb-3">Images</h6>

    <!-- Upload drop-zone -->
    <label for="images"
        class="d-flex flex-column justify-content-center align-items-center text-muted rounded border"
        style="width: 100px; height: 100px; cursor:pointer; border:2px dashed #d9d9d9;">
        <span style="font-size: 2rem; line-height: 1;">&#43;</span>
        <small>Add&nbsp;Images</small>
    </label>

    <!-- Previews will be added here -->
    <div id="image-preview-container" class="d-flex align-items-start flex-wrap gap-3 mt-3">
        <!-- JS will append thumbnails here -->
    </div>

    <!-- Hidden native file input -->
    <input type="file" class="d-none" name="images[]" id="images" multiple accept="image/*">
    <div class="text-danger" id="error-images"></div>
</div>



                                <!-- ================= Pricing & Stocks ================= -->
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">Pricing & Stocks</h6>

                                    <div class="mb-3">
                                        <label class="form-label me-3">Product Type <span
                                                class="text-danger">*</span></label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="product_type"
                                                id="product_type_single" value="single">
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
                                                <label for="sku">SKU</label>
                                                <input type="text" class="form-control" name="sku"
                                                    id="sku">
                                                <div class="text-danger" id="error-sku"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="quantity"
                                                    id="quantity" min="0">
                                                <div class="text-danger" id="error-quantity"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="price">Price <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" class="form-control" name="price"
                                                    id="price" min="0">
                                                <div class="text-danger" id="error-price"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="tax">Tax (%)</label>
                                                <input type="number" step="0.01" class="form-control" name="tax"
                                                    id="tax" min="0">
                                                <div class="text-danger" id="error-tax"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="tax_type">Tax Type</label>
                                                <select class="form-select" name="tax_type" id="tax_type">
                                                    <option value="">Select</option>
                                                    <option value="fixed">Fixed</option>
                                                    <option value="percentage">Percentage</option>
                                                </select>
                                                <div class="text-danger" id="error-tax_type"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="discount_type">Discount Type</label>
                                                <select class="form-select" name="discount_type" id="discount_type">
                                                    <option value="">Select</option>
                                                    <option value="percentage">Percentage</option>
                                                    <option value="fixed">Fixed</option>
                                                </select>
                                                <div class="text-danger" id="error-discount_type"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="discount_value">Discount Value</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="discount_value" id="discount_value" min="0">
                                                <div class="text-danger" id="error-discount_value"></div>
                                            </div>
                                        </div>
                                    </div>
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
    const previewContainer = document.getElementById('image-preview-container');
    const imageInput = document.getElementById('images');

    let oldImages = []; // { id, image }
    let newImagePreviews = []; // { file, preview }

    imageInput.addEventListener('change', handleFiles);

    function handleFiles(e) {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(event) {
                newImagePreviews.push({
                    file,
                    preview: event.target.result
                });
                renderPreviews();
            };
            reader.readAsDataURL(file);
        });

        // 👇 Clear input so same file can be selected again later
        imageInput.value = '';
    }

    function renderPreviews() {
        previewContainer.innerHTML = '';

        // Old images
        oldImages.forEach((image, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative d-inline-block me-2 mb-2';
            wrapper.style.width = '100px';

            const img = document.createElement('img');
            img.src = image.image;
            img.className = 'img-fluid rounded';
            img.style.height = '100px';
            img.style.objectFit = 'cover';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 translate-middle rounded-circle';
            btn.style.padding = '0 6px';
            btn.innerHTML = '&times;';
            btn.addEventListener('click', () => {
                oldImages.splice(index, 1);
                renderPreviews();
            });

            wrapper.appendChild(img);
            wrapper.appendChild(btn);
            previewContainer.appendChild(wrapper);
        });

        // New images
        newImagePreviews.forEach((entry, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative d-inline-block me-2 mb-2';
            wrapper.style.width = '100px';

            const img = document.createElement('img');
            img.src = entry.preview;
            img.className = 'img-fluid rounded';
            img.style.height = '100px';
            img.style.objectFit = 'cover';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 translate-middle rounded-circle';
            btn.style.padding = '0 6px';
            btn.innerHTML = '&times;';
            btn.addEventListener('click', () => {
                newImagePreviews.splice(index, 1);
                renderPreviews();
            });

            wrapper.appendChild(img);
            wrapper.appendChild(btn);
            previewContainer.appendChild(wrapper);
        });
    }

    async function populateDropdown(url, elementId, selectedId = null) {
        const res = await fetch(url);
        const data = await res.json();
        const select = document.getElementById(elementId);
        select.innerHTML = '<option value="" disabled>Select</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.text = item.name;
            if (selectedId && item.id == selectedId) option.selected = true;
            select.appendChild(option);
        });
    }

    async function loadProductDetails() {
        try {
            const res = await fetch(`http://127.0.0.1:8000/api/products/${productId}`);
            const product = await res.json();

            document.getElementById('name').value = product.name || '';
            document.getElementById('item_code').value = product.item_code || '';
            document.getElementById('status').value = product.status || '';

            if (product.singlevariant) {
                document.getElementById('sku').value = product.singlevariant.sku || '';
                document.getElementById('quantity').value = product.singlevariant.stock_quantity || '';
                document.getElementById('price').value = product.singlevariant.price || '';
                document.getElementById('tax').value = product.singlevariant.tax || '';
                document.getElementById('tax_type').value = product.singlevariant.tax_type || '';
                document.getElementById('discount_type').value = product.singlevariant.discount_type || '';
                document.getElementById('discount_value').value = product.singlevariant.discount || '';
            }

            if (product.product_type === 'single') {
                document.getElementById('product_type_single').checked = true;
                document.getElementById('single-product-fields').style.display = 'block';
            } else {
                document.getElementById('product_type_variable').checked = true;
                document.getElementById('single-product-fields').style.display = 'none';
            }

            await Promise.all([
                populateDropdown('http://127.0.0.1:8000/api/stores', 'store_id', product.store_id),
                // Comment for now - Maybe will come in use later
                // populateDropdown('http://127.0.0.1:8000/api/users', 'user_id', product.user_id),
                populateDropdown('http://127.0.0.1:8000/api/categories', 'category_id', product.category_id),
                populateDropdown('http://127.0.0.1:8000/api/brands', 'brand_id', product.brand_id),
                populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id', product.vendor_id)
            ]);

            if (Array.isArray(product.images)) {
                const baseImageUrl = 'http://127.0.0.1:8004';
                oldImages = product.images.map(img => ({
                    id: img.id,
                    image: baseImageUrl + img.image
                }));
            }

            renderPreviews();
        } catch (err) {
            alert('Failed to load product data.');
            console.error(err);
        }
    }

    document.getElementById('edit-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        // ✅ Only add from newImagePreviews
        newImagePreviews.forEach(entry => {
            formData.append('images[]', entry.file);
        });

        oldImages.forEach(img => {
            formData.append('existing_images[]', img.id);
        });

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/products/${productId}`, {
                method: 'POST',
                body: formData
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
            alert('Network or server error');
            console.error(err);
        }
    });

    loadProductDetails();
</script>

@endsection

