@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="buttons text-end mb-3">
            <a href="{{ route('product-variants.create') }}" class="btn btn-primary">Add Product Variant</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Product Variants</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Tax</th>
                                <th>Tax Type</th>
                                <th>Discount</th>
                                <th>Discount Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="variant-table-body">
                            <!-- JS will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteVariantModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="delete-variant-form">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_variant_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product variant?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
@parent
<script>
    async function loadVariants() {
        const res = await fetch('http://127.0.0.1:8000/api/product-variants', { credentials: 'include' });
        const variants = await res.json();
        const tbody = document.getElementById('variant-table-body');
        tbody.innerHTML = '';

        variants.forEach(variant => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${variant.product?.name || ''}</td>
                <td>${variant.sku}</td>
                <td>${variant.price}</td>
                <td>${variant.stock_quantity}</td>
                <td>${variant.tax}</td>
                <td>${variant.tax_type}</td>
                <td>${variant.discount}</td>
                <td>${variant.discount_type}</td>
                <td>
                    <a href="/product-variants/edit/${variant.id}" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger ms-1" onclick="openDeleteModal(${variant.id})">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function openDeleteModal(id) {
        document.getElementById('delete_variant_id').value = id;
        new bootstrap.Modal(document.getElementById('deleteVariantModal')).show();
    }

    document.getElementById('delete-variant-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('delete_variant_id').value;

        const res = await fetch(`http://127.0.0.1:8000/api/product-variants/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' },
            credentials: 'include'
        });

        if (res.ok) {
            bootstrap.Modal.getInstance(document.getElementById('deleteVariantModal')).hide();
            loadVariants();
        } else {
            alert('Delete failed');
        }
    });

    loadVariants();
</script>
@endsection
