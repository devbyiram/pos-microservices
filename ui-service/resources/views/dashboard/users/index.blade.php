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
            <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
        </div>

        <!-- Row 1 -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-md-flex align-items-center justify-content-between">
                            <h4 class="card-title">Users</h4>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-body">
                                    <tr>
                                        <td colspan="7"></td>
                                    </tr>
                                </tbody>
                            </table>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-delete-btn" class="btn btn-danger">Yes, Delete</button>
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
        const tbody = document.getElementById('users-table-body');
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');
        let userIdToDelete = null;

        function showSuccess(message) {
            successText.textContent = message;
            successMessage.classList.remove('d-none');
            successMessage.scrollIntoView({ behavior: 'smooth' });

            setTimeout(() => {
                successMessage.classList.add('d-none');
                successText.textContent = '';
            }, 3000);
        }

        function loadUsers() {
            fetch('http://127.0.0.1:8000/api/users', {
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
                        tbody.innerHTML = '<tr><td colspan="7">No users found.</td></tr>';
                        return;
                    }

                    data.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.role ?? 'N/A'}</td>
                            <td><span class="badge bg-${user.status == 1 ? 'success' : 'secondary'}">${user.status == 1 ? 'Active' : 'Inactive'}</span></td>
                            <td>
                                <a href="/users/edit/${user.id}" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete(${user.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-danger">Error fetching users: ${error.message}</td></tr>`;
                    console.error('Error fetching users:', error);
                });
        }

        // Show delete confirmation modal
        window.confirmDelete = function (id) {
            userIdToDelete = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Handle confirmation
        document.getElementById('confirm-delete-btn').addEventListener('click', function () {
            if (!userIdToDelete) return;

            fetch(`http://127.0.0.1:8000/api/users/${userIdToDelete}`, {
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
                        loadUsers();
                        showSuccess('User deleted successfully!');
                    } else {
                        alert('Error: ' + (body.message || 'Failed to delete user'));
                    }
                })
                .catch(error => {
                    console.error('Error deleting user:', error);
                });
        });

        // Initial load
        loadUsers();
    });
</script>
@endsection
