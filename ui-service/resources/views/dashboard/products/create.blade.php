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
                                            <select class="form-select" name="store_id" id="store_id">
                                                <option value="" disabled selected>Select Store</option>
                                            </select>
                                            <div class="text-danger" id="error-store_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="category_id">Category</label>
                                            <select class="form-select" name="category_id" id="category_id">
                                                <option value="" disabled selected>Select Category</option>
                                            </select>
                                            <div class="text-danger" id="error-category_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="brand_id">Brand</label>
                                            <select class="form-select" name="brand_id" id="brand_id">
                                                <option value="" disabled selected>Select Brand</option>
                                            </select>
                                            <div class="text-danger" id="error-brand_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="vendor_id">Vendor</label>
                                            <select class="form-select" name="vendor_id" id="vendor_id">
                                                <option value="" disabled selected>Select Vendor</option>
                                            </select>
                                            <div class="text-danger" id="error-vendor_id"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="status">Status</label>
                                            <select class="form-select" name="status" id="status">
                                                <option value="" disabled selected>Select Status</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                            <div class="text-danger" id="error-status"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ================= Images ================= -->
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">Images</h6>

                                    <!-- Upload drop‑zone -->
                                    <div id="image-preview-container" class="d-flex align-items-start flex-wrap gap-3">
                                        <label for="images"
                                            class="d-flex flex-column justify-content-center align-items-center text-muted rounded border"
                                            style="width: 100px; height: 100px; cursor:pointer; border:2px dashed #d9d9d9;">
                                            <span style="font-size: 2rem; line-height: 1;">&#43;</span>
                                            <small>Add&nbsp;Images</small>
                                        </label>
                                    </div>

                                    <!-- Hidden native file input -->
                                    <input type="file" class="d-none" name="images[]" id="images" multiple
                                        accept="image/*">
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

                                    <!-- Single‑product block -->
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
                                                    <option value="" >Select</option>
                                                    <option value="fixed">Fixed</option>
                                                    <option value="percentage">Percentage</option>
                                                </select>
                                                <div class="text-danger" id="error-tax_type"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="discount_type">Discount Type</label>
                                                <select class="form-select" name="discount_type" id="discount_type">
                                                    <option value="" >Select</option>
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


                                <!-- ================= Variable Variant Block ================= -->
 <div id="variant-fields" class="mb-4" style="display: none;">
                                <h6 class="fw-semibold mb-3">Variants</h6>
                                <button type="button" class="btn btn-success mb-3" id="add-variant-block">+ Add Variant</button>
                                <div id="variant-blocks" class="d-flex flex-column gap-3"></div>
                            </div>

                                <!-- ================= Variable Variant Block End ================= -->
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
        // ================= Populate dropdowns =================
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
                // Comment for now - Maybe will come in use later
                // populateDropdown('http://127.0.0.1:8000/api/users', 'user_id'),
                populateDropdown('http://127.0.0.1:8000/api/categories', 'category_id'),
                populateDropdown('http://127.0.0.1:8000/api/brands', 'brand_id'),
                populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id')
            ]);
        }
        loadDropdowns();

        // ================= Product‑type toggle =================
        document.querySelectorAll('input[name="product_type"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.getElementById('single-product-fields').style.display =
                    document.getElementById('product_type_single').checked ? 'block' : 'none';
            });
        });

        // ================= Image previews =================
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');
        let dt = new DataTransfer(); // Holds selected files

        imageInput.addEventListener('change', handleFiles);

        function handleFiles(e) {
            Array.from(e.target.files).forEach(file => dt.items.add(file));
            imageInput.files = dt.files; // Sync back to input
            renderPreviews();
        }

        function renderPreviews() {
            // Remove existing thumbs (keep the upload box -> :first-child)
            Array.from(previewContainer.children).forEach((child, idx) => {
                if (idx) child.remove();
            });

            Array.from(imageInput.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative';
                    wrapper.style.width = '100px';

                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'img-fluid rounded';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className =
                        'btn btn-sm btn-danger position-absolute top-0 end-0 translate-middle rounded-circle';
                    btn.style.padding = '0 6px';
                    btn.innerHTML = '&times;';
                    btn.addEventListener('click', () => removeImage(index));

                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeImage(removeIndex) {
            const newDt = new DataTransfer();
            Array.from(imageInput.files).forEach((file, index) => {
                if (index !== removeIndex) newDt.items.add(file);
            });
            dt = newDt;
            imageInput.files = dt.files;
            renderPreviews();
        }

        // ================= Form submission =================
        document.getElementById('create-product-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('[id^="error-"]').forEach(el => el.innerText = '');

            const formData = new FormData(this);
            // Replace images with the manually managed FileList (dt)
            formData.delete('images[]');
            Array.from(imageInput.files).forEach(file => formData.append('images[]', file));

            try {
                const response = await fetch('http://127.0.0.1:8000/api/products', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const result = await response.json();

                if (!response.ok) {
                   if (result.errors) {
    // Remove all old error messages
    document.querySelectorAll('.validation-error').forEach(el => el.remove());

    Object.entries(result.errors).forEach(([field, messages]) => {
        let input;

        // Check if it's a nested variant field (e.g. variants.0.sku)
        const variantMatch = field.match(/^variants\.(\d+)\.(\w+)$/);
        if (variantMatch) {
            const [_, index, name] = variantMatch;
            input = document.querySelector(`[name="variants[${index}][${name}]"]`);
        }
        // ✅ Handle image validation errors like images or images.0
        else if (field.startsWith('images')) {
            input = document.getElementById('images'); // file input
        }
        else {
            input = document.querySelector(`[name="${field}"]`);
        }

        if (input) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger validation-error mt-1';
            errorDiv.innerText = messages[0];

            // If it's the image input, insert error after preview container
            if (field.startsWith('images')) {
                const previewContainer = document.getElementById('image-preview-container');
                previewContainer.insertAdjacentElement('afterend', errorDiv);
            } else {
                input.insertAdjacentElement('afterend', errorDiv);
            }
        }
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


let variantAttributes = {};

// Load attributes from API
async function loadVariantAttributes() {
    const res = await fetch('http://127.0.0.1:8000/api/variant-attributes');
    const data = await res.json();
    variantAttributes = {};
    data.forEach(attr => {
        if (!variantAttributes[attr.name]) {
            variantAttributes[attr.name] = [];
        }
        if (!variantAttributes[attr.name].includes(attr.value)) {
            variantAttributes[attr.name].push(attr.value);
        }
    });
}
loadVariantAttributes();

// Toggle type sections
const radios = document.querySelectorAll('input[name="product_type"]');
radios.forEach(radio => radio.addEventListener('change', () => {
    const isVariable = document.getElementById('product_type_variable').checked;
    document.getElementById('variant-fields').style.display = isVariable ? 'block' : 'none';
    document.getElementById('single-product-fields').style.display = isVariable ? 'none' : 'block';
}));

// Add Variant Block
const addBtn = document.getElementById('add-variant-block');
const container = document.getElementById('variant-blocks');

addBtn.addEventListener('click', () => {
    const index = container.children.length;
    const attrNames = Object.keys(variantAttributes);

    const block = document.createElement('div');
    block.className = 'border rounded p-3 position-relative variant-block';
    block.innerHTML = `
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-variant">&times;</button>
        <div class="row g-3">
            <div class="col-md-3"><label>SKU</label><input type="text" name="variants[${index}][sku]" class="form-control"></div>
            <div class="col-md-2"><label>Price</label><input type="number" name="variants[${index}][price]" class="form-control"></div>
            <div class="col-md-2"><label>Stock</label><input type="number" name="variants[${index}][stock_quantity]" class="form-control"></div>
            <div class="col-md-2"><label>Tax</label><input type="number" name="variants[${index}][tax]" class="form-control"></div>
            <div class="col-md-3"><label>Tax Type</label>
                <select name="variants[${index}][tax_type]" class="form-select">
                    <option value="fixed">Fixed</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>
            <div class="col-md-3"><label>Discount</label><input type="number" name="variants[${index}][discount]" class="form-control"></div>
            <div class="col-md-3"><label>Discount Type</label>
                <select name="variants[${index}][discount_type]" class="form-select">
                    <option value="fixed">Fixed</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>
            <div class="col-md-12">
                <label>Attributes</label>
                <div class="d-flex flex-wrap gap-3" id="attr-checkboxes-${index}">
                    ${attrNames.map(attr => `
                        <label class="form-check">
                            <input class="form-check-input variant-attr-checkbox" type="checkbox" data-attribute="${attr}" data-index="${index}"> ${attr}
                        </label>
                    `).join('')}
                </div>
            </div>
            <div class="row g-3" id="attr-fields-${index}"></div>
        </div>
    `;
    container.appendChild(block);
});

// Remove variant block
container.addEventListener('click', e => {
    if (e.target.classList.contains('remove-variant')) {
        e.target.closest('.variant-block').remove();
    }
});

// Toggle custom input fields for attributes
container.addEventListener('change', e => {
    if (e.target.classList.contains('variant-attr-checkbox')) {
        const attr = e.target.dataset.attribute;
        const index = e.target.dataset.index;
        const container = document.getElementById(`attr-fields-${index}`);
        const fieldName = `variants[${index}][${attr.toLowerCase()}]`;

        if (e.target.checked) {
            const div = document.createElement('div');
            div.className = 'col-md-4 mb-2';
            div.innerHTML = `<label>${attr}</label><input type="text" name="${fieldName}" class="form-control">`;
            container.appendChild(div);
        } else {
            const input = container.querySelector(`[name="${fieldName}"]`);
            if (input) input.parentElement.remove();
        }
    }
});
    </script>
@endsection
