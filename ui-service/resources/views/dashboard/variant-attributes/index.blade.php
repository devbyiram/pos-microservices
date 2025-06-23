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
            <a href="{{ route('variant-attributes.create') }}" class="btn btn-primary">Add Variant Attribute</a>
        </div>

        <!-- Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-md-flex align-items-center justify-content-between">
                            <h4 class="card-title">Variant Attributes</h4>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Value</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="variant-attributes-table-body">
                                    <tr>
                                        <td colspan="5">Loading...</td>
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
              <h5 class="modal-title" id="deleteModalLabel">Delete Variant Attribute</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              Are you sure you want to delete this variant attribute?
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
        let attributeIdToDelete = null;

        const tbody = document.getElementById('variant-attributes-table-body');
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');

        function showSuccess(message) {
            successText.textContent = message;
            successMessage.classList.remove('d-none');
            successMessage.scrollIntoView({ behavior: 'smooth' });

            setTimeout(() => {
                successMessage.classList.add('d-none');
                successText.textContent = '';
            }, 3000);
        }

        function loadVariantAttributes() {
            fetch('http://127.0.0.1:8000/api/variant-attributes', {
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
                    tbody.innerHTML = '<tr><td colspan="5">No variant attributes found.</td></tr>';
                    return;
                }

                data.forEach(attribute => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${attribute.id}</td>
                        <td>${attribute.name}</td>
                        <td>${attribute.value}</td>
                        <td>
                            <a href="/variant-attributes/edit/${attribute.id}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(${attribute.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                tbody.innerHTML = `<tr><td colspan="5" class="text-danger">Error fetching data: ${error.message}</td></tr>`;
                console.error('Error fetching attributes:', error);
            });
        }

        // Show delete confirmation modal
        window.confirmDelete = function (id) {
            attributeIdToDelete = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function () {
            if (!attributeIdToDelete) return;

            fetch(`http://127.0.0.1:8000/api/variant-attributes/${attributeIdToDelete}`, {
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
                    loadVariantAttributes();
                    showSuccess('Variant attribute deleted successfully!');
                } else {
                    alert('Error: ' + (body.message || 'Failed to delete variant attribute'));
                }
            })
            .catch(error => {
                console.error('Error deleting variant attribute:', error);
            });
        });

        loadVariantAttributes();
    });
</script>
@endsection
