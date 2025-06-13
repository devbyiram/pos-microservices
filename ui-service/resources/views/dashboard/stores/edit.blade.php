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
                    <h5 class="card-title fw-semibold mb-4">Edit Store</h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="edit-store-form">
                                @csrf
                                <input type="hidden" id="store_id" value="{{ $store_id }}">

                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Store Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                    <small class="text-danger" id="error-name"></small>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Store</button>
                                <a href="{{ route('stores.index') }}" class="btn btn-secondary">Cancel</a>
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
        const storeId = document.getElementById('store_id').value;

        // Load store data
        async function loadStoreDetails() {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/stores/${storeId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                const store = await res.json();
                document.getElementById('name').value = store.name;
            } catch (err) {
                console.error('Failed to load store:', err);
                alert('Failed to load store data.');
            }
        }

        // Initialize form
        loadStoreDetails();

        // Handle form submit
        document.getElementById('edit-store-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            document.getElementById('error-name').textContent = '';

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/stores/${storeId}`, {
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
                    if (result.errors && result.errors.name) {
                        document.getElementById('error-name').textContent = result.errors.name[0];
                    } else {
                        alert('Error: ' + result.message);
                    }
                } else {
                    window.location.href = '/stores'; // Redirect to index page
                }
            } catch (err) {
                console.error('Update failed:', err);
                alert('Server or network error occurred.');
            }
        });
    </script>
@endsection
