@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a type="button" href="{{ route('categories.index') }}" class="btn btn-primary mb-4">
                        Back
                    </a>
                    <h5 class="card-title fw-semibold mb-4">Edit Category</h5>

                    <div class="card">
                        <div class="card-body">
                            <form id="edit-category-form">
                                @csrf
                                <input type="hidden" id="category_id" value="{{ $category_id }}">

                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                    <small class="text-danger" id="error-name"></small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select" name="store_id" id="store_id">
                                        <option value="" disabled selected>Select store</option>
                                    </select>
                                    <small class="text-danger" id="error-store_id"></small>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Category</button>
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
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
        const categoryId = document.getElementById('category_id').value;

        // Load all stores into dropdown
        async function loadStores(selectedStoreId = null) {
            try {
                const res = await fetch('http://127.0.0.1:8000/api/stores', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'include'
                });

                const stores = await res.json();
                const storeSelect = document.getElementById('store_id');

                stores.forEach(store => {
                    const option = document.createElement('option');
                    option.value = store.id;
                    option.textContent = store.name;

                    if (store.id == selectedStoreId) {
                        option.selected = true;
                    }

                    storeSelect.appendChild(option);
                });

            } catch (err) {
                console.error('Error loading stores:', err);
                alert('Could not load store list');
            }
        }

        // Load category data
        async function loadCategoryDetails() {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/categories/${categoryId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                    credentials: 'include'
                });

                const category = await res.json();
                document.getElementById('name').value = category.name;

                // Load stores and select the one linked to this category
                await loadStores(category.store_id);

            } catch (err) {
                console.error('Failed to load category:', err);
                alert('Failed to load category data.');
            }
        }

        // Initialize form with category and store data
        loadCategoryDetails();

        // Submit update
        document.getElementById('edit-category-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            ['name', 'store_id'].forEach(id => {
                const el = document.getElementById(`error-${id}`);
                if (el) el.textContent = '';
            });

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const res = await fetch(`http://127.0.0.1:8000/api/categories/${categoryId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify(jsonData)
                });

                const result = await res.json();

                if (!res.ok) {
                    const errors = result.errors || {};
                    if (errors.name) {
                        document.getElementById('error-name').textContent = errors.name[0];
                    }
                    if (errors.store_id) {
                        document.getElementById('error-store_id').textContent = errors.store_id[0];
                    }

                    if (!errors.name && !errors.store_id && result.message) {
                        alert('Error: ' + result.message);
                    }

                } else {
                    window.location.href = '/categories'; // Redirect to index
                }

            } catch (err) {
                console.error('Update failed:', err);
                alert('Server or network error occurred.');
            }
        });
    </script>
@endsection
