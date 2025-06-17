@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('purchases.index') }}" class="btn btn-primary mb-4">Back</a>
                <h5 class="card-title fw-semibold mb-4">Add Purchase</h5>

                <div id="success-message" class="alert alert-success d-none">
                    <span id="success-text"></span>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="create-purchase-form">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="store_id">Store</label>
                                <select class="form-select" id="store_id" name="store_id">
                                    <option value="" disabled selected>Select Store</option>
                                </select>
                                <div class="text-danger" id="error-store_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="user_id">User</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="" disabled selected>Select User</option>
                                </select>
                                <div class="text-danger" id="error-user_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="vendor_id">Vendor</label>
                                <select class="form-select" id="vendor_id" name="vendor_id">
                                    <option value="" disabled selected>Select Vendor</option>
                                </select>
                                <div class="text-danger" id="error-vendor_id"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="purchase_date">Purchase Date</label>
                                <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                                <div class="text-danger" id="error-purchase_date"></div>
                            </div>

                            <!-- Purchase Items Table -->
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
                                <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" readonly>
                                <div class="text-danger" id="error-total_amount"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Purchase</button>
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
    let productsList = [];

    async function loadDropdowns() {
        const endpoints = {
            store: 'stores',
            user: 'users',
            vendor: 'vendors',
            product: 'products'
        };

        for (const [key, endpoint] of Object.entries(endpoints)) {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/${endpoint}`, {
                    credentials: 'include'
                });
                const data = await res.json();
                const select = document.getElementById(`${key}_id`);
                if (key !== 'product') {
                    data.forEach(item => {
                        select.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                } else {
                    productsList = data;
                }
            } catch (err) {
                console.error(`Failed to load ${key}:`, err);
            }
        }
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

    function addItemRow() {
        const tbody = document.querySelector('#items-table tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                <select class="form-select product_id" name="items[][product_id]">
                    ${productsList.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="items[][quantity]" class="form-control quantity" min="1" value="1"></td>
            <td><input type="number" name="items[][price]" class="form-control price" min="0.01" step="0.01" value="0.00"></td>
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

    document.addEventListener('DOMContentLoaded', () => {
        loadDropdowns();

        document.getElementById('add-item-btn').addEventListener('click', addItemRow);

        document.getElementById('create-purchase-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            ['store_id', 'user_id', 'vendor_id', 'purchase_date', 'total_amount']
            .forEach(f => document.getElementById(`error-${f}`).innerText = '');

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

            try {
                const response = await fetch('http://127.0.0.1:8000/api/purchases', {
                    method: 'POST',
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
                        if (errorElem) {
                            errorElem.innerText = messages.join(', ');
                        }
                    });
                } else if (response.ok) {
                    document.getElementById('success-text').innerText = 'Purchase created successfully!';
                    document.getElementById('success-message').classList.remove('d-none');
                    this.reset();
                    document.querySelector('#items-table tbody').innerHTML = '';
                    updateTotalAmount();
                    setTimeout(() => document.getElementById('success-message').classList.add('d-none'), 5000);
                } else {
                    alert('Error: ' + (result.message || 'Something went wrong.'));
                }
            } catch (err) {
                alert('Network/server error');
            }
        });
    });
</script>
@endsection
