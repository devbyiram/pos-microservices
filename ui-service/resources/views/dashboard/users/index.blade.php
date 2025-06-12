@extends('dashboard.partials.main')
@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">

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
    </div>
@endsection


@section('js')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('users-table-body');
            if (!tbody) {
                console.error("Element with ID 'users-table-body' not found.");
                return;
            }

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
                    const tbody = document.getElementById('users-table-body');
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
             <td><span class="badge bg-${user.status === 'active' ? 'success' : 'secondary'}">${user.status}</span></td>
            <td>
               <a href="/users/edit/${user.id}" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger">Delete</button>
            </td>
        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    const tbody = document.getElementById('users-table-body');
                    tbody.innerHTML =
                        `<tr><td colspan="7" class="text-danger">Error fetching users: ${error.message}</td></tr>`;
                    console.error('Error fetching users:', error);
                });
        });
    </script>
@endsection
