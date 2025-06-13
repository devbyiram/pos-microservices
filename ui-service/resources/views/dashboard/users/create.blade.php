@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                     <a type="button" href="{{route('users.index')}}" class="btn btn-primary  mb-4">
                               Back
                     </a>
                    <h5 class="card-title fw-semibold mb-4">Add User</h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="create-user-form" method="POST" action="">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Enter full name" >
                                    <div class="text-danger" id="error-name"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="Enter email" >
                                    <div class="text-danger" id="error-email"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Enter password" >
                                    <div class="text-danger" id="error-password"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select" name="store_id" id="store_id" >
                                        <option value="" disabled selected>Select store</option>
                                    </select>
                                    <div class="text-danger" id="error-store_id"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Create User</button>
                                <button type="reset" class="btn btn-secondary">Cancel</button>
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
        document.getElementById('create-user-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous validation errors
            ['name', 'email', 'password', 'store_id'].forEach(field => {
                const el = document.getElementById(`error-${field}`);
                if (el) el.innerText = '';
            });

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('http://127.0.0.1:8000/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify(jsonData)
                });

                const result = await response.json();

                if (response.status === 422) {
                    const errors = result.errors || {};
                    Object.entries(errors).forEach(([field, messages]) => {
                        const errorDiv = document.getElementById(`error-${field}`);
                        if (errorDiv) {
                            errorDiv.innerText = messages.join(', ');
                        }
                    });
                } else if (!response.ok) {
                    alert('Error creating user: ' + (result.message || JSON.stringify(result)));
                } else {
                    alert('User created successfully!');
                    this.reset();
                }

            } catch (err) {
                console.error('Network error:', err);
                alert('Network or server error');
            }
        });
    </script>

    <script>
        // Load stores dynamically
        async function loadStores() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/stores'); // via API Gateway
                const stores = await response.json();

                const storeSelect = document.getElementById('store_id');
                storeSelect.innerHTML = '<option value="" disabled selected>Select a store</option>';

                stores.forEach(store => {
                    const option = document.createElement('option');
                    option.value = store.id;
                    option.textContent = store.name;
                    storeSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading stores:', error);
                alert('Failed to load stores. Please try again later.');
            }
        }

        loadStores(); // Call this on page load
    </script>
@endsection
