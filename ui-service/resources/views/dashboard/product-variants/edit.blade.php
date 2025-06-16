@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="/product-variants" class="btn btn-primary mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Edit Product Variant</h5>

                <div class="card">
                    <div class="card-body">
                        <form id="edit-product-variant-form">
                            @csrf
                            <input type="hidden" id="variant_id" value="{{ $product_variant_id }}">

                            <div class="form-group mb-3">
                                <label for="sku">SKU</label>
                                <input type="text" class="form-control" name="sku" id="sku">
                                <div class="text-danger" id="error-sku"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" name="price" id="price">
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
                                    <option value="fixed">Fixed</option>
                                    <option value="percentage">Percentage</option>
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
                                    <option value="fixed">Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                                <div class="text-danger" id="error-discount_type"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Variant</button>
                            <a href="/product-variants" class="btn btn-secondary">Cancel</a>
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
    const variantId = document.getElementById('variant_id').value;

    async function loadVariantDetails() {
        try {
            const res = await fetch(`http://127.0.0.1:8000/api/product-variants/${variantId}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const variant = await res.json();

            document.getElementById('sku').value = variant.sku;
            document.getElementById('price').value = variant.price;
            document.getElementById('stock_quantity').value = variant.stock_quantity;
            document.getElementById('tax').value = variant.tax;
            document.getElementById('tax_type').value = variant.tax_type;
            document.getElementById('discount').value = variant.discount;
            document.getElementById('discount_type').value = variant.discount_type;

        } catch (err) {
            console.error('Failed to load product variant:', err);
            alert('Failed to load variant data.');
        }
    }

    loadVariantDetails();

    document.getElementById('edit-product-variant-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        ['sku', 'price', 'stock_quantity', 'tax', 'tax_type', 'discount', 'discount_type'].forEach(field => {
            document.getElementById(`error-${field}`).innerText = '';
        });

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/product-variants/${variantId}`, {
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
                window.location.href = '/product-variants';
            }
        } catch (err) {
            console.error('Update failed:', err);
            alert('Server or network error occurred.');
        }
    });
</script>
@endsection
