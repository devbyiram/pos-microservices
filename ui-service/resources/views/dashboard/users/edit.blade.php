@extends('dashboard.partials.main')
@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Edit User</h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="edit-user-form">
                                @csrf
                                <input type="hidden" id="user_id" value="{{ $user_id }}">

                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <small class="text-danger" id="error-name"></small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <small class="text-danger" id="error-email"></small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <small class="text-danger" id="error-password"></small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select" name="store_id" id="store_id" required>
                                        <option value="" disabled selected>Select a store</option>
                                    </select>
                                    <small class="text-danger" id="error-store_id"></small>
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

        // Load store list and optionally select one
        async function loadStores(selectedId = null) {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/stores');
                const stores = await response.json();

                const storeSelect = document.getElementById('store_id');
                storeSelect.innerHTML = '<option value="" disabled>Select a store</option>';

                stores.forEach(store => {
                    const option = document.createElement('option');
                    option.value = store.id;
                    option.textContent = store.name;
                    if (store.id == selectedId) option.selected = true;
                    storeSelect.appendChild(option);
                });
            } catch (err) {
                console.error('Failed to load stores:', err);
                alert('Failed to load stores.');
            }
        }

        // Load user details and auto-fill form
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

                const store = user.stores?.[0];
                await loadStores(store?.id);
            } catch (err) {
                console.error('Failed to load user:', err);
                alert('Failed to load user data.');
            }
        }

        // Initialize form
        loadUserDetails();

        // Handle form submit
        document.getElementById('edit-user-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear all previous error messages
            ['name', 'email', 'password', 'store_id'].forEach(field => {
                const errorEl = document.getElementById(`error-${field}`);
                if (errorEl) errorEl.textContent = '';
            });

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

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
                        // Show validation messages under fields
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const errorEl = document.getElementById(`error-${field}`);
                            if (errorEl) errorEl.textContent = messages[0];
                        });
                    } else {
                        alert('Error: ' + result.message);
                    }
                } else {
                    window.location.href = '/users'; // Redirect to index
                }
            } catch (err) {
                console.error('Update failed:', err);
                alert('Server or network error occurred.');
            }
        });
    </script>
@endsection
