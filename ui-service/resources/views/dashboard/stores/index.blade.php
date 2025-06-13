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
                <a href="{{ route('stores.create') }}" class="btn btn-primary">Add Store</a>
            </div>

            <!-- Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="d-md-flex align-items-center justify-content-between">
                                <h4 class="card-title">Stores</h4>
                            </div>
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-hover">
                                    <thead class="table">
                                        <tr>
                                            <th>ID</th>
                                            <th>Store Name</th>
                                            <th>Status</th>
                                            {{-- <th>Created By</th> --}}
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stores-table-body">
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
@endsection

@section('js')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.getElementById('stores-table-body');
            const successMessage = document.getElementById('success-message');
            const successText = document.getElementById('success-text');

            if (!tbody) {
                console.error("Element with ID 'stores-table-body' not found.");
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

            function loadStores() {
                fetch('http://127.0.0.1:8000/api/stores', {
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
                            tbody.innerHTML = '<tr><td colspan="4">No stores found.</td></tr>';
                            return;
                        }

                        data.forEach(store => {
                            const row = document.createElement('tr');

                            const creatorName = store.users && store.users.length
                                ? store.users.map(u => u.name).join(', ')
                                : 'N/A';

                            row.innerHTML = `
                                <td>${store.id}</td>
                                <td>${store.name}</td>
                                <td><span class="badge bg-${store.status == 1 ? 'success' : 'secondary'}">${store.status == 1 ? 'Active' : 'Inactive'}</span></td>
                                {{-- <td>${creatorName}</td> --}}
                                <td>
                                    <a href="/stores/edit/${store.id}" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteStore(${store.id})">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Error fetching stores: ${error.message}</td></tr>`;
                        console.error('Error fetching stores:', error);
                    });
            }

            window.deleteStore = function (storeId) {
                fetch(`http://127.0.0.1:8000/api/stores/${storeId}`, {
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
                            loadStores();
                            showSuccess('Store deleted successfully!');
                        } else {
                            alert('Error: ' + (body.message || 'Failed to delete store'));
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting store:', error);
                    });
            };

            // Initial load
            loadStores();
        });
    </script>
@endsection
