@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a type="button" href="{{ route('stores.index') }}" class="btn btn-primary mb-4">
                        Back
                    </a>
                    <h5 class="card-title fw-semibold mb-4">Add Store</h5>

                    {{-- Success message --}}
                    <div id="success-message" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                        <span id="success-text"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="create-store-form" method="POST" action="">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Store Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Enter store name">
                                    <div class="text-danger" id="error-name"></div>
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

                                <button type="submit" class="btn btn-primary">Create Store</button>
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
        document.getElementById('create-store-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous validation errors
            ['name', 'status'].forEach(field => {
                const el = document.getElementById(`error-${field}`);
                if (el) el.innerText = '';
            });

            // Hide previous success message
            document.getElementById('success-message').classList.add('d-none');

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('http://127.0.0.1:8000/api/stores', {
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
                    alert('Error creating store: ' + (result.message || JSON.stringify(result)));
                } else {
                    // Show success message
                    document.getElementById('success-text').innerText = 'Store created successfully!';
                    document.getElementById('success-message').classList.remove('d-none');
                    document.getElementById('success-message').scrollIntoView({ behavior: 'smooth' });

                    // Reset the form
                    this.reset();

                    // Optional: auto-hide after 5 seconds
                    setTimeout(() => {
                        document.getElementById('success-message').classList.add('d-none');
                    }, 5000);
                }

            } catch (err) {
                console.error('Network error:', err);
                alert('Network or server error');
            }
        });
    </script>
@endsection
