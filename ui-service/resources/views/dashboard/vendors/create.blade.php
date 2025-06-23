@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('vendors.index') }}" class="btn btn-primary mb-4">Back</a>
                    <h5 class="card-title fw-semibold mb-4">Add Vendor</h5>

                    <div id="success-message" class="alert alert-success d-none">
                        <span id="success-text"></span>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="create-vendor-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Vendor Name</label>
                                                <input type="text" class="form-control" name="name" id="name">
                                                <div class="text-danger" id="error-name"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" name="email" id="email">
                                                <div class="text-danger" id="error-email"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text" class="form-control" name="phone" id="phone">
                                                <div class="text-danger" id="error-phone"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <textarea class="form-control" name="address" id="address"></textarea>
                                                <div class="text-danger" id="error-address"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="store_id">Store</label>
                                    <select class="form-select" id="store_id" name="store_id">
                                        <option value="" disabled selected>Select Store</option>
                                    </select>
                                    <div class="text-danger" id="error-store_id"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Create Vendor</button>
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
    async function loadStores() {
        const res = await fetch('http://127.0.0.1:8000/api/stores', { credentials: 'include' });
        const data = await res.json();
        const select = document.getElementById('store_id');
        data.forEach(store => {
            select.innerHTML += `<option value="${store.id}">${store.name}</option>`;
        });
    }

    document.addEventListener('DOMContentLoaded', loadStores);

    document.getElementById('create-vendor-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        ['name', 'email', 'phone', 'address', 'store_id'].forEach(f => {
            document.getElementById(`error-${f}`).innerText = '';
        });

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('http://127.0.0.1:8000/api/vendors', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (response.status === 422) {
                Object.entries(result.errors).forEach(([field, messages]) => {
                    document.getElementById(`error-${field}`).innerText = messages.join(', ');
                });
            } else if (response.ok) {
                document.getElementById('success-text').innerText = 'Vendor created successfully!';
                document.getElementById('success-message').classList.remove('d-none');
                this.reset();
                setTimeout(() => document.getElementById('success-message').classList.add('d-none'), 5000);
            } else {
                alert('Error: ' + (result.message || 'Something went wrong.'));
            }
        } catch (err) {
            alert('Network/server error');
        }
    });
</script>
@endsection
