@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a type="button" href="{{ route('brands.index') }}" class="btn btn-primary mb-4">
                        Back
                    </a>
                    <h5 class="card-title fw-semibold mb-4">Edit Brand</h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="edit-brand-form">
                                @csrf
                                <input type="hidden" id="brand_id" value="{{ $brand_id }}">

                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Brand Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                    <small class="text-danger" id="error-name"></small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select" name="store_id" id="store_id">
                                        <option value="" disabled selected>Select store</option>
                                    </select>
                                    <div class="text-danger" id="error-store_id"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Brand</button>
                                <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancel</a>
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
        const brandId = document.getElementById('brand_id').value;
        const storeSelect = document.getElementById('store_id');

        async function loadStores() {
            try {
                const res = await fetch('http://127.0.0.1:8000/api/stores', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                const stores = await res.json();

                stores.forEach(store => {
                    const option = document.createElement('option');
                    option.value = store.id;
                    option.textContent = store.name;
                    storeSelect.appendChild(option);
                });
            } catch (err) {
                console.error('Failed to load stores:', err);
                alert('Could not load stores.');
            }
        }

        async function loadBrandDetails() {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/brands/${brandId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                const brand = await res.json();
                document.getElementById('name').value = brand.name;
                await loadStores(); // Wait for stores to load before selecting
                document.getElementById('store_id').value = brand.store_id;
            } catch (err) {
                console.error('Failed to load brand:', err);
                alert('Could not load brand data.');
            }
        }

        loadBrandDetails();

        document.getElementById('edit-brand-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            document.getElementById('error-name').textContent = '';
            document.getElementById('error-store_id').textContent = '';

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/brands/${brandId}`, {
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
                        if (result.errors.name) {
                            document.getElementById('error-name').textContent = result.errors.name[0];
                        }
                        if (result.errors.store_id) {
                            document.getElementById('error-store_id').textContent = result.errors.store_id[0];
                        }
                    } else {
                        alert('Error: ' + result.message);
                    }
                } else {
                    window.location.href = '/brands'; // Redirect after success
                }
            } catch (err) {
                console.error('Update failed:', err);
                alert('Server or network error occurred.');
            }
        });
    </script>
@endsection
