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
                    <h5 class="card-title fw-semibold mb-4">Add Brand</h5>

                    {{-- Success message --}}
                    <div id="success-message" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                        <span id="success-text"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="create-brand-form" method="POST" action="">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Brand Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Enter brand name">
                                    <div class="text-danger" id="error-name"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select" name="store_id" id="store_id">
                                        <option value="" disabled selected>Select store</option>
                                    </select>
                                    <div class="text-danger" id="error-store_id"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Create Brand</button>
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
        document.addEventListener('DOMContentLoaded', function () {
            const storeSelect = document.getElementById('store_id');

            fetch('http://127.0.0.1:8000/api/stores', {
                method: 'GET',
                credentials: 'include'
            })
                .then(response => response.json())
                .then(data => {
                    data.forEach(store => {
                        const option = document.createElement('option');
                        option.value = store.id;
                        option.textContent = store.name;
                        storeSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Failed to load stores:', error);
                });

            document.getElementById('create-brand-form').addEventListener('submit', async function (e) {
                e.preventDefault();

                // Clear validation messages
                ['name', 'store_id'].forEach(field => {
                    const el = document.getElementById(`error-${field}`);
                    if (el) el.innerText = '';
                });

                document.getElementById('success-message').classList.add('d-none');

                const formData = new FormData(this);
                const jsonData = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('http://127.0.0.1:8000/api/brands', {
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
                        alert('Error creating brand: ' + (result.message || JSON.stringify(result)));
                    } else {
                        document.getElementById('success-text').innerText = 'Brand created successfully!';
                        document.getElementById('success-message').classList.remove('d-none');
                        document.getElementById('success-message').scrollIntoView({ behavior: 'smooth' });

                        this.reset();

                        setTimeout(() => {
                            document.getElementById('success-message').classList.add('d-none');
                        }, 5000);
                    }

                } catch (err) {
                    console.error('Network error:', err);
                    alert('Network or server error');
                }
            });
        });
    </script>
@endsection
