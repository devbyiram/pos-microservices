@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">

            <!-- Success Message Alert -->
            <div id="success-message" class="alert alert-success d-none" role="alert">
                <span id="success-text"></span>
            </div>

            <div class="buttons text-end mb-3">
                <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
            </div>

            <!-- Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="d-md-flex align-items-center justify-content-between">
                                <h4 class="card-title">Products</h4>
                            </div>
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-hover">
                                    <thead class="table">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Store</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Vendor</th>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-table-body">
                                        <tr>
                                            <td colspan="11">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-delete-btn" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @parent
    <script>
         const baseImageUrl = "{{ $base_image_url }}";
        document.addEventListener('DOMContentLoaded', function() {
            let productIdToDelete = null;

            const tbody = document.getElementById('products-table-body');
            const successMessage = document.getElementById('success-message');
            const successText = document.getElementById('success-text');

            function showSuccess(message) {
                successText.textContent = message;
                successMessage.classList.remove('d-none');
                successMessage.scrollIntoView({
                    behavior: 'smooth'
                });

                setTimeout(() => {
                    successMessage.classList.add('d-none');
                    successText.textContent = '';
                }, 3000);
            }

            function loadProducts() {
                fetch('http://127.0.0.1:8000/api/products', {
                        method: 'GET',
                        credentials: 'include'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        tbody.innerHTML = '';

                        if (!Array.isArray(data) || data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="11">No products found.</td></tr>';
                            return;
                        }

                        data.forEach(product => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                        <td>${product.id}</td>
                <td class="d-flex align-items-center gap-2">
     <img src="${baseImageUrl}${product.images?.[0]?.image || '/images/default.png'}"
                                 alt="${product.name}"
                                 width="30" height="30"
                                 style="object-fit: cover; border-radius: 4px;">
    <span>${product.name}</span>
</td>
                        <td>${product.store?.name || 'N/A'}</td>
                        <td>${product.category?.name || 'N/A'}</td>
                        <td>${product.brand?.name || 'N/A'}</td>
                        <td>${product.vendor?.name || 'N/A'}</td>
                        <td>${product.user?.name || 'N/A'}</td>
                        <td><span class="badge bg-${product.status == 1 ? 'success' : 'secondary'}">${product.status == 1 ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <a href="/products/edit/${product.id}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(${product.id})">Delete</button>
                        </td>
                    `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        tbody.innerHTML =
                            `<tr><td colspan="11" class="text-danger">Error fetching products: ${error.message}</td></tr>`;
                        console.error('Error fetching products:', error);
                    });
            }

            // Show delete confirmation modal
            window.confirmDelete = function (id) {
                productIdToDelete = id;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }

            document.getElementById('confirm-delete-btn').addEventListener('click', function () {
                if (!productIdToDelete) return;

                fetch(`http://127.0.0.1:8000/api/products/${productIdToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                        },
                        credentials: 'include'
                    })
                    .then(response => response.json()
                        .then(data => ({
                            status: response.status,
                            body: data
                        })))
                    .then(({
                        status,
                        body
                    }) => {
                        if (status === 200) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                            modal.hide();
                            loadProducts();
                            showSuccess('Product deleted successfully!');
                        } else {
                            alert('Error: ' + (body.message || 'Failed to delete product'));
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting product:', error);
                    });
            });

            loadProducts();
        });
    </script>
@endsection
