@extends('dashboard.partials.main')
@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a type="button" href="{{ route('users.index') }}" class="btn btn-primary  mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Edit User</h5>
                <div class="card">
                    <div class="card-body">
                        <form id="edit-user-form">
                            @csrf
                            <input type="hidden" id="user_id" value="{{ $user_id }}">

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                                <small class="text-danger" id="error-name"></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email">
                                <small class="text-danger" id="error-email"></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-danger" id="error-password"></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <div class="text-danger" id="error-password_confirmation"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="store_id" class="form-label">Store</label>
                                <select class="form-select" name="store_id[]" id="store_id" multiple></select>
                                <small class="text-danger" id="error-store_id"></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="" disabled selected>Select status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="text-danger" id="error-status"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="{{ route('users.create') }}" class="btn btn-secondary">Cancel</a>
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
    const userId = document.getElementById('user_id').value;

    $(document).ready(function () {
        $('#store_id').select2({
            placeholder: "Select store(s)",
            allowClear: true,
            width: '100%'
        });

        async function loadStores(selectedIds = []) {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/stores');
        const stores = await response.json();

        const storeSelect = document.getElementById('store_id');
        storeSelect.innerHTML = '';

        stores.forEach(store => {
            const option = document.createElement('option');
            option.value = store.id;
            option.textContent = store.name;
            if (selectedIds.includes(String(store.id))) {
                option.selected = true;
            }
            storeSelect.appendChild(option);
        });
    } catch (err) {
        console.error('Failed to load stores:', err);
        alert('Failed to load stores.');
    }
}

async function loadUserDetails() {
    try {
        const res = await fetch(`http://127.0.0.1:8000/api/users/${userId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        const user = await res.json();
        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('status').value = user.status;

        const storeIds = user.stores?.map(store => String(store.id)) || [];
        await loadStores(storeIds);
    } catch (err) {
        console.error('Failed to load user:', err);
        alert('Failed to load user data.');
    }
}


        loadUserDetails();

        document.getElementById('edit-user-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear errors
            ['name', 'email', 'password', 'store_id', 'status', 'password_confirmation'].forEach(field => {
                const errorEl = document.getElementById(`error-${field}`);
                if (errorEl) errorEl.textContent = '';
            });

            // Prepare data
            const form = this;
            const formData = new FormData(form);
            const storeIds = $('#store_id').val(); // Select2 returns array

            const jsonData = {
                name: form.name.value,
                email: form.email.value,
                password: form.password.value,
                password_confirmation: form.password_confirmation.value,
                status: form.status.value,
                store_id: storeIds
            };

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify(jsonData)
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const errorEl = document.getElementById(`error-${field}`);
                            if (errorEl) errorEl.textContent = messages[0];
                        });
                    } else {
                        alert(result.message || 'Unknown error occurred.');
                    }
                } else {
                    window.location.href = '/users';
                }
            } catch (err) {
                console.error('Update failed:', err);
                alert('Server or network error occurred.');
            }
        });
    });
</script>
@endsection
