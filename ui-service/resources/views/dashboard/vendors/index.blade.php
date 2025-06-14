@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div id="success-message" class="alert alert-success d-none">
                <span id="success-text"></span>
            </div>

            <div class="buttons text-end mb-3">
                <a href="{{ route('vendors.create') }}" class="btn btn-primary">Add Vendor</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Vendors</h4>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Store</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="vendors-table-body">
                                <tr><td colspan="6">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@parent
<script>
    const tbody = document.getElementById('vendors-table-body');

    function showSuccess(message) {
        const box = document.getElementById('success-message');
        document.getElementById('success-text').innerText = message;
        box.classList.remove('d-none');
        setTimeout(() => box.classList.add('d-none'), 3000);
    }

    function loadVendors() {
        fetch('http://127.0.0.1:8000/api/vendors', { credentials: 'include' })
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6">No vendors found.</td></tr>';
                    return;
                }

                data.forEach(v => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${v.id}</td>
                            <td>${v.name}</td>
                            <td>${v.email}</td>
                            <td>${v.phone}</td>
                            <td>${v.store?.name || 'N/A'}</td>
                            <td>
                                <a href="/vendors/edit/${v.id}" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteVendor(${v.id})">Delete</button>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error loading vendors</td></tr>`;
            });
    }

    function deleteVendor(id) {
        fetch(`http://127.0.0.1:8000/api/vendors/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' },
            credentials: 'include'
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status }) => {
            if (status === 200) {
                loadVendors();
                showSuccess('Vendor deleted successfully!');
            } else {
                alert('Failed to delete vendor');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', loadVendors);
</script>
@endsection
