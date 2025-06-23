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
                <a href="{{ route('brands.create') }}" class="btn btn-primary">Add Brand</a>
            </div>

            <!-- Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="d-md-flex align-items-center justify-content-between">
                                <h4 class="card-title">Brands</h4>
                            </div>
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-hover">
                                    <thead class="table">
                                        <tr>
                                            <th>ID</th>
                                            <th>Brand Name</th>
                                            <th>Store</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="brands-table-body">
                                        <tr>
                                            <td colspan="4"></td>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Brand</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this brand?
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
        document.addEventListener('DOMContentLoaded', function () {
            let brandIdToDelete = null;

            const tbody = document.getElementById('brands-table-body');
            const successMessage = document.getElementById('success-message');
            const successText = document.getElementById('success-text');

            if (!tbody) {
                console.error("Element with ID 'brands-table-body' not found.");
                return;
            }

            function showSuccess(message) {
                successText.textContent = message;
                successMessage.classList.remove('d-none');
                successMessage.scrollIntoView({ behavior: 'smooth' });

                setTimeout(() => {
                    successMessage.classList.add('d-none');
                    successText.textContent = '';
                }, 3000);
            }

            function loadBrands() {
                fetch('http://127.0.0.1:8000/api/brands', {
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
                            tbody.innerHTML = '<tr><td colspan="4">No brands found.</td></tr>';
                            return;
                        }

                        data.forEach(brand => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                                <td>${brand.id}</td>
                                <td>${brand.name}</td>
                                <td>${brand.store ? brand.store.name : 'N/A'}</td>
                                <td>
                                    <a href="/brands/edit/${brand.id}" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(${brand.id})">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Error fetching brands: ${error.message}</td></tr>`;
                        console.error('Error fetching brands:', error);
                    });
            }

            // Show delete confirmation modal
            window.confirmDelete = function (id) {
                brandIdToDelete = id;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }

            document.getElementById('confirm-delete-btn').addEventListener('click', function () {
                if (!brandIdToDelete) return;

                fetch(`http://127.0.0.1:8000/api/brands/${brandIdToDelete}`, {
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
                    .then(({ status, body }) => {
                        if (status === 200) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                            modal.hide();
                            loadBrands();
                            showSuccess('Brand deleted successfully!');
                        } else {
                            alert('Error: ' + (body.message || 'Failed to delete brand'));
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting brand:', error);
                    });
            });

            // Initial load
            loadBrands();
        });
    </script>
@endsection
