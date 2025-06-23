@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('variant-attributes.index') }}" class="btn btn-primary mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Edit Variant Attribute</h5>

                <div class="card">
                    <div class="card-body">
                        <form id="edit-variant-attribute-form">
                            @csrf
                            <input type="hidden" id="variant_attribute_id" value="{{ $variant_attribute_id }}">

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

                            <button type="submit" class="btn btn-primary">Update Variant Attribute</button>
                            <a href="{{ route('variant-attributes.index') }}" class="btn btn-secondary">Cancel</a>
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
    const variantAttributeId = document.getElementById('variant_attribute_id').value;

    async function loadVariantAttribute() {
        try {
            const res = await fetch(`http://127.0.0.1:8000/api/variant-attributes/${variantAttributeId}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            document.getElementById('name').value = data.name;
            document.getElementById('value').value = data.value;
        } catch (err) {
            console.error('Failed to load variant attribute:', err);
            alert('Failed to load variant attribute data.');
        }
    }

    loadVariantAttribute();

    document.getElementById('edit-variant-attribute-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        ['name', 'value'].forEach(field => {
            document.getElementById(`error-${field}`).innerText = '';
        });

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/variant-attributes/${variantAttributeId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.errors) {
                    Object.entries(result.errors).forEach(([field, messages]) => {
                        document.getElementById(`error-${field}`).innerText = messages[0];
                    });
                } else {
                    alert('Error: ' + result.message);
                }
            } else {
                window.location.href = '/variant-attributes';
            }
        } catch (err) {
            console.error('Update failed:', err);
            alert('Server or network error occurred.');
        }
    });
</script>
@endsection
