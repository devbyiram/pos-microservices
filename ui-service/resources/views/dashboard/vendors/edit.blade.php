@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <a href="{{ route('vendors.index') }}" class="btn btn-primary mb-4">Back</a>
            <h5 class="card-title fw-semibold mb-4">Edit Vendor</h5>

            <div class="card">
                <div class="card-body">
                    <form id="edit-vendor-form">
                        @csrf
                        <input type="hidden" id="vendor_id" value="{{ $vendor_id }}">

                        <div class="form-group mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Vendor Name</label>
                                        <input type="text" class="form-control" id="name" name="name">
                                        <div class="text-danger" id="error-name"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
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
                                        <input type="text" class="form-control" id="phone" name="phone">
                                        <div class="text-danger" id="error-phone"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address"></textarea>
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

                        <button type="submit" class="btn btn-primary">Update Vendor</button>
                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@parent
<script>
    const vendorId = document.getElementById('vendor_id').value;

    async function loadStores(selectedStoreId = null) {
        const res = await fetch('http://127.0.0.1:8000/api/stores', { credentials: 'include' });
        const stores = await res.json();
        const select = document.getElementById('store_id');
        stores.forEach(store => {
            select.innerHTML += `<option value="${store.id}" ${store.id == selectedStoreId ? 'selected' : ''}>${store.name}</option>`;
        });
    }

    async function loadVendor() {
        const res = await fetch(`http://127.0.0.1:8000/api/vendors/${vendorId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        });
        const data = await res.json();
        document.getElementById('name').value = data.name;
        document.getElementById('email').value = data.email;
        document.getElementById('phone').value = data.phone;
        document.getElementById('address').value = data.address;
        await loadStores(data.store_id);
    }

    document.addEventListener('DOMContentLoaded', loadVendor);

    document.getElementById('edit-vendor-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        ['name', 'email', 'phone', 'address', 'store_id'].forEach(f => {
            document.getElementById(`error-${f}`).innerText = '';
        });

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        const res = await fetch(`http://127.0.0.1:8000/api/vendors/${vendorId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify(jsonData)
        });

        const result = await res.json();
        if (res.status === 422) {
            Object.entries(result.errors).forEach(([f, msg]) => {
                document.getElementById(`error-${f}`).innerText = msg.join(', ');
            });
        } else if (res.ok) {
            window.location.href = '/vendors';
        } else {
            alert(result.message || 'Something went wrong.');
        }
    });
</script>
@endsection
