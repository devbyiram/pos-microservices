@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('variant-attributes.index') }}" class="btn btn-primary mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Add Variant Attribute</h5>

                <div id="success-message" class="alert alert-success d-none">
                    <span id="success-text"></span>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="create-variant-attribute-form">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Attribute Name</label>
                                <input type="text" class="form-control" name="name" id="name">
                                <div class="text-danger" id="error-name"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="value">Value</label>
                                <input type="text" class="form-control" name="value" id="value">
                                <div class="text-danger" id="error-value"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="status">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="" disabled selected>Select status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="text-danger" id="error-status"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Variant Attribute</button>
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
    document.getElementById('create-variant-attribute-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        ['name', 'value', 'status'].forEach(f => document.getElementById(`error-${f}`).innerText = '');

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('http://127.0.0.1:8000/api/variant-attributes', {
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
                document.getElementById('success-text').innerText = 'Variant attribute created successfully!';
                document.getElementById('success-message').classList.remove('d-none');
                this.reset();
                setTimeout(() => document.getElementById('success-message').classList.add('d-none'), 5000);
                document.getElementById('status').value = '';
            } else {
                alert('Error: ' + (result.message || 'Something went wrong.'));
            }
        } catch (err) {
            alert('Network/server error');
        }
    });
</script>
@endsection
