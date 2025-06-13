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
            const nameError = document.getElementById('error-name');
            nameError.innerText = '';

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
                    if (errors.name) {
                        nameError.innerText = errors.name.join(', ');
                    }
                } else if (!response.ok) {
                    alert('Error creating store: ' + (result.message || JSON.stringify(result)));
                } else {
                    alert('Store created successfully!');
                    this.reset();
                }

            } catch (err) {
                console.error('Network error:', err);
                alert('Network or server error');
            }
        });
    </script>
@endsection
