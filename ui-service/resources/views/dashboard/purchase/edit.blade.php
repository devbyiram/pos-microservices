@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('purchases.index') }}" class="btn btn-primary mb-4">Back</a>
                    <h5 class="card-title fw-semibold mb-4">Edit Purchase</h5>

                    <div id="success-message" class="alert alert-success d-none">
                        <span id="success-text"></span>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="edit-purchase-form">
                                @csrf

                                <input type="hidden" id="purchase_id" value="{{ $purchase_id }}">

                                <div class="form-group mb-3">
                                    <label for="store_id">Store</label>
                                    <select class="form-select" id="store_id" name="store_id"></select>
                                    <div class="text-danger" id="error-store_id"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="vendor_id">Vendor</label>
                                    <select class="form-select" id="vendor_id" name="vendor_id"></select>
                                    <div class="text-danger" id="error-vendor_id"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                                    <div class="text-danger" id="error-purchase_date"></div>
                                </div>

                                <h5 class="mt-4">Purchase Items</h5>
                                <table class="table table-bordered" id="items-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-info mb-3" id="add-item-btn">Add Item</button>

                                <div class="form-group mb-3">
                                    <label for="total_amount">Total Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="total_amount"
                                        name="total_amount" readonly>
                                    <div class="text-danger" id="error-total_amount"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Purchase</button>
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
        const purchaseId = document.getElementById('purchase_id').value;
        let productsList = [];

        async function populateDropdown(url, elementId, selectedId = null) {
            const res = await fetch(url, {
                credentials: 'include'
            });
            const data = await res.json();
            const select = document.getElementById(elementId);
            select.innerHTML = `<option disabled selected>Select ${elementId.replace('_id', '')}</option>`;
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.text = item.name;
                if (selectedId && item.id == selectedId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }

        function updateTotalAmount() {
            let total = 0;
            document.querySelectorAll('#items-table tbody tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                total += qty * price;
                row.querySelector('.line-total').innerText = (qty * price).toFixed(2);
            });
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        function addItemRow(item = null) {
            const tbody = document.querySelector('#items-table tbody');
            const row = document.createElement('tr');

            const options = productsList.map(p =>
                `<option value="${p.id}" ${item && item.product_id == p.id ? 'selected' : ''}>${p.name}</option>`
            ).join('');

            row.innerHTML = `
            <td>
                <select class="form-select product_id" name="items[][product_id]">${options}</select>
            </td>
            <td><input type="number" name="items[][quantity]" class="form-control quantity" min="1" value="${item?.quantity || 1}"></td>
            <td><input type="number" name="items[][price]" class="form-control price" min="0.01" step="0.01" value="${item?.price || 0}"></td>
            <td class="line-total">0.00</td>
            <td><button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button></td>
        `;

            tbody.appendChild(row);
            updateTotalAmount();

            row.querySelector('.quantity').addEventListener('input', updateTotalAmount);
            row.querySelector('.price').addEventListener('input', updateTotalAmount);
            row.querySelector('.remove-item-btn').addEventListener('click', () => {
                row.remove();
                updateTotalAmount();
            });
        }

        async function loadPurchase() {
            const res = await fetch(`http://127.0.0.1:8000/api/purchases/${purchaseId}`, {
                credentials: 'include'
            });
            const data = await res.json();

            document.getElementById('purchase_date').value = data.purchase_date;
            document.getElementById('total_amount').value = data.total_amount;

            await Promise.all([
                populateDropdown('http://127.0.0.1:8000/api/stores', 'store_id', data.store_id),
                populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id', data.vendor_id),
                loadProducts(data.items)
            ]);
        }

        async function loadProducts(items = []) {
            const res = await fetch(`http://127.0.0.1:8000/api/products`, {
                credentials: 'include'
            });
            productsList = await res.json();

            if (items.length) {
                items.forEach(item => addItemRow(item));
            } else {
                addItemRow();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadPurchase();

            document.getElementById('add-item-btn').addEventListener('click', () => addItemRow());

            document.getElementById('edit-purchase-form').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const items = [];

                document.querySelectorAll('#items-table tbody tr').forEach(row => {
                    items.push({
                        product_id: row.querySelector('.product_id').value,
                        quantity: row.querySelector('.quantity').value,
                        price: row.querySelector('.price').value
                    });
                });

                const data = Object.fromEntries(formData.entries());
                data.items = items;

                const response = await fetch(`http://127.0.0.1:8000/api/purchases/${purchaseId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.status === 422) {
                    Object.entries(result.errors).forEach(([field, messages]) => {
                        const errorElem = document.getElementById(`error-${field}`);
                        if (errorElem) errorElem.innerText = messages.join(', ');
                    });
                } else if (response.ok) {
                    window.location.href = "{{ route('purchases.index') }}";
                } else {
                    alert('Error: ' + (result.message || 'Something went wrong.'));
                }
            });
        });
    </script>
@endsection
