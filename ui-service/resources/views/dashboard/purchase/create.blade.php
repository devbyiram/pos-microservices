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

                    <form id="create-purchase-form">
                        @csrf
                        <div class="form-group mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="store_id">Store</label>
                                        <select class="form-select" id="store_id" name="store_id">
                                            <option value="" disabled selected>Select Store</option>
                                        </select>
                                        <div class="text-danger" id="error-store_id"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vendor_id">Vendor</label>
                                        <select class="form-select" id="vendor_id" name="vendor_id">
                                            <option value="" disabled selected>Select Vendor</option>
                                        </select>
                                        <div class="text-danger" id="error-vendor_id"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="purchase_date">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                            <div class="text-danger" id="error-purchase_date"></div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Received">Received</option>
                                            <option value="Pending">Pending</option>
                                        </select>
                                        <div class="text-danger" id="error-status"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="payment_status">Payment Status</label>
                                        <select class="form-select" id="payment_status" name="payment_status">
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                        <div class="text-danger" id="error-payment_status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Purchase Items</h5>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Purchase Price</th>
                                    <th>Discount</th>
                                    <th>Tax (%)</th>
                                    <th>Tax Amt</th>
                                    <th>Unit Cost</th>
                                    <th>Total Cost</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="text-danger mb-2" id="error-items"></div>
                        <button type="button" class="btn btn-sm btn-info mb-3" id="add-item-btn">Add Item</button>
                          <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="order_tax">Order Tax</label>
                            <input type="number" step="0.01" class="form-control bg-light" id="order_tax"
                                name="order_tax" readonly>
                            <div class="text-danger" id="error-order_tax"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="order_discount">Order Discount</label>
                            <input type="number" step="0.01" class="form-control bg-light" id="order_discount"
                                name="order_discount" readonly>
                            <div class="text-danger" id="error-order_discount"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="shipping">Shipping</label>
                            <input type="number" step="0.01" class="form-control" id="shipping" name="shipping">
                            <div class="text-danger" id="error-shipping"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="total_amount">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount"
                                readonly>
                            <div class="text-danger" id="error-total_amount"></div>
                        </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Purchase</button>
                    </form>
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
    let grandTotal = 0;
    let totalTax = 0;
    let totalDiscount = 0;

    document.querySelectorAll('#items-table tbody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.quantity').value) || 0;
        const priceInput = row.querySelector('.price');
        const priceError = row.querySelector('.price-error');
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(row.querySelector('.discount').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;

        // Validate purchase price
        // if (price <= 0) {
        //     priceError.innerText = 'Purchase price is required';
        // } else {
        //     priceError.innerText = '';
        // }

        const discounted = price - discount;
        const taxPerUnit = discounted * (tax / 100);
        const taxAmount = taxPerUnit * qty;
        const unitCost = discounted + taxPerUnit;
        const total = unitCost * qty;

        row.querySelector('.tax-amount').value = taxAmount.toFixed(2);
        row.querySelector('.unit-cost').value = unitCost.toFixed(2);
        row.querySelector('.total-cost').value = total.toFixed(2);

        grandTotal += total;
        totalTax += taxAmount;
        totalDiscount += discount * qty;
    });

    const shipping = parseFloat(document.getElementById('shipping').value) || 0;

    document.getElementById('order_tax').value = totalTax.toFixed(2);
    document.getElementById('order_discount').value = totalDiscount.toFixed(2);
    document.getElementById('total_amount').value = (grandTotal + shipping).toFixed(2);
}


        function addItemRow() {
            const tbody = document.querySelector('#items-table tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select class="form-select product_id" name="items[][product_id]">
                        ${productsList.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                    </select>
                    <div class="text-danger error-product_id"></div>
                </td>
                <td>
                    <input type="number" name="items[][quantity]" class="form-control quantity" value="1" min="1">
                    <div class="text-danger error-quantity"></div>
                </td>
                <td>
                    <input type="number" name="items[][purchase_price]" class="form-control price" step="0.01" value="0.00">
                    <div class="text-danger error-purchase_price"></div>
                </td>

                <td>
                    <input type="number" name="items[][discount]" class="form-control discount" step="0.01" value="0.00">
                    <div class="text-danger error-discount"></div>
                </td>
                <td>
                    <input type="number" name="items[][tax]" class="form-control tax" step="0.01" value="0.00">
                    <div class="text-danger error-tax"></div>
                </td>
                <td>
                    <input type="text" name="items[][tax_amount]" class="form-control tax-amount" readonly>
                    <div class="text-danger error-tax_amount"></div>
                </td>
                <td>
                    <input type="text" name="items[][unit_cost]" class="form-control unit-cost" readonly>
                    <div class="text-danger error-unit_cost"></div>
                </td>
                <td>
                    <input type="text" name="items[][total_cost]" class="form-control total-cost" readonly>
                    <div class="text-danger error-total_cost"></div>
                </td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button></td>
            `;
            tbody.appendChild(row);
            row.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('input', updateTotalAmount);
            });
            row.querySelector('.remove-item-btn').addEventListener('click', () => {
                row.remove();
                updateTotalAmount();
            });
            updateTotalAmount();
        }

        function clearErrors() {
            document.querySelectorAll('[id^="error-"]').forEach(el => el.innerText = '');
        }

        function displayValidationErrors(errors) {
            clearValidationErrors();

            Object.entries(errors).forEach(([field, messages]) => {
                if (field.startsWith("items.")) {
                    const parts = field.split(".");
                    const rowIndex = parseInt(parts[1]);
                    const fieldName = parts[2];
                    const row = document.querySelectorAll("#items-table tbody tr")[rowIndex];
                    if (row) {
                        const errorDiv = row.querySelector(`.error-${fieldName}`);
                        if (errorDiv) errorDiv.innerText = messages[0];
                    }
                } else {
                    const input = document.getElementById(field);
                    if (input) {
                        let errorContainer = input.nextElementSibling;
                        if (errorContainer && errorContainer.classList.contains("text-danger")) {
                            errorContainer.innerText = messages[0];
                        } else {
                            const div = document.createElement("div");
                            div.classList.add("text-danger");
                            div.innerText = messages[0];
                            input.parentNode.appendChild(div);
                        }
                    }
                }
            });
        }

        function clearValidationErrors() {
            document.querySelectorAll('.text-danger').forEach(el => el.innerText = '');
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadDropdowns();
            addItemRow();

            document.getElementById('add-item-btn').addEventListener('click', addItemRow);

            ['order_tax', 'order_discount', 'shipping'].forEach(id => {
                document.getElementById(id).addEventListener('input', updateTotalAmount);
            });

            document.getElementById('create-purchase-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors();

                const items = [];
                document.querySelectorAll('#items-table tbody tr').forEach(row => {
                    items.push({
                        product_id: row.querySelector('.product_id').value,
                        quantity: row.querySelector('.quantity').value,
                        purchase_price: row.querySelector('.price').value,
                        discount: row.querySelector('.discount').value,
                        tax: row.querySelector('.tax').value,
                        tax_amount: row.querySelector('.tax-amount').value,
                        unit_cost: row.querySelector('.unit-cost').value,
                        total_cost: row.querySelector('.total-cost').value
                    });
                });

                if (items.length === 0) {
                    document.getElementById('error-items').innerText =
                        'At least one item is required.';
                    return;
                }

                const data = {
                    store_id: document.getElementById('store_id').value,
                    vendor_id: document.getElementById('vendor_id').value,
                    purchase_date: document.getElementById('purchase_date').value,
                    status: document.getElementById('status').value,
                    payment_status: document.getElementById('payment_status').value,
                    order_tax: document.getElementById('order_tax').value,
                    order_discount: document.getElementById('order_discount').value,
                    shipping: document.getElementById('shipping').value,
                    total_amount: document.getElementById('total_amount').value,
                    items: items
                };

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
                        displayValidationErrors(result.errors);
                    } else if (response.ok) {
                        document.getElementById('success-text').innerText =
                            'Purchase created successfully!';
                        document.getElementById('success-message').classList.remove('d-none');
                        this.reset();
                        document.querySelector('#items-table tbody').innerHTML = '';
                        addItemRow();
                        updateTotalAmount();
                        setTimeout(() => document.getElementById('success-message').classList.add(
                            'd-none'), 5000);
                    } else {
                        alert(result.message || 'Something went wrong.');
                    }
                } catch (err) {
                    console.error('Error:', err);
                    alert('Network/server error');
                }
            });
        });
    </script>
@endsection
