@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a type="button" href="{{ route('users.index') }}" class="btn btn-primary mb-4">
                        Back
                    </a>
                    <h5 class="card-title fw-semibold mb-4">Add User</h5>

                    {{-- ✅ Success message --}}
                    <div id="success-message" class="alert alert-success d-none">
                        <span id="success-text"></span>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="create-user-form" method="POST" action="">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter full name">
                                    <div class="text-danger" id="error-name"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter email">
                                    <div class="text-danger" id="error-email"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter password">
                                    <div class="text-danger" id="error-password"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm password">
                                    <div class="text-danger" id="error-password_confirmation"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select" name="store_id[]" id="store_id" multiple>
                                        <!-- Options will be loaded dynamically -->
                                    </select>
                                    <div class="text-danger" id="error-store_id"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#store_id').select2({
                placeholder: "Select store(s)",
                allowClear: true,
                width: '100%'
            });

            // Load stores into Select2
            async function loadStores() {
                try {
                    const response = await fetch('http://127.0.0.1:8000/api/stores');
                    const stores = await response.json();

                    const storeSelect = $('#store_id');
                    storeSelect.empty().append('<option></option>');

                    stores.forEach(store => {
                        const option = new Option(store.name, store.id, false, false);
                        storeSelect.append(option);
                    });

                    storeSelect.trigger('change');
                } catch (error) {
                    console.error('Error loading stores:', error);
                    alert('Failed to load stores. Please try again later.');
                }
            }

            loadStores();

            // Handle form submission
            $('#create-user-form').on('submit', async function (e) {
                e.preventDefault();

                // Clear validation messages
                ['name', 'email', 'password', 'password_confirmation', 'store_id', 'status'].forEach(field => {
                    $(`#error-${field}`).text('');
                });

                const formData = new FormData(this);
                const jsonData = Object.fromEntries(formData.entries());
                jsonData.store_id = $('#store_id').val(); // for multiselect
                jsonData.status = $('#status').val();

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
                            $(`#error-${field}`).text(messages.join(', '));
                        });
                    } else if (!response.ok) {
                        alert('Error creating user: ' + (result.message || JSON.stringify(result)));
                    } else {
                        $('#success-text').text('User created successfully!');
                        $('#success-message').removeClass('d-none');
                        document.getElementById('success-message').scrollIntoView({ behavior: 'smooth' });

                        this.reset();
                        $('#store_id').val(null).trigger('change');
                        $('#status').val('').trigger('change');
                    }
                } catch (err) {
                    console.error('Network error:', err);
                    alert('Network or server error');
                }
            });
        });
    </script>
@endsection
