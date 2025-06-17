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

                    <div class="card">
                        <div class="card-body">
                            <form id="edit-purchase-form">
                                @csrf
                                <input type="hidden" id="purchase_id" value="{{ $purchase_id }}">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="store_id">Store</label>
                                        <select class="form-select" id="store_id" name="store_id"></select>
                                        <div class="text-danger" id="error-store_id"></div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="vendor_id">Vendor</label>
                                        <select class="form-select" id="vendor_id" name="vendor_id"></select>
                                        <div class="text-danger" id="error-vendor_id"></div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_date">Purchase Date</label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                                        <div class="text-danger" id="error-purchase_date"></div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Pending">Pending</option>
                                            <option value="Received">Received</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="payment_status">Payment Status</label>
                                        <select class="form-select" id="payment_status" name="payment_status">
                                            <option value="Unpaid">Unpaid</option>
                                            <option value="Paid">Paid</option>
                                        </select>
                                    </div>
                                </div>

                                <h5 class="mt-4">Purchase Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="items-table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Purchase Price</th>
                                                <th>Discount</th>
                                                <th>Tax %</th>
                                                <th>Tax Amt</th>
                                                <th>Unit Cost</th>
                                                <th>Total Cost</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <button type="button" class="btn btn-sm btn-info mb-3" id="add-item-btn">Add Item</button>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="order_discount">Order Discount</label>
                                        <input type="number" class="form-control" id="order_discount"
                                            name="order_discount">
                                        <div class="text-danger" id="error-order_discount"></div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="order_tax">Order Tax</label>
                                        <input type="number" class="form-control" id="order_tax" name="order_tax" readonly>
                                        <div class="text-danger" id="error-order_tax"></div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="shipping">Shipping</label>
                                        <input type="number" class="form-control" id="shipping" name="shipping">
                                        <div class="text-danger" id="error-shipping"></div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="total_amount">Total Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="total_amount"
                                            name="total_amount" readonly>
                                        <div class="text-danger" id="error-total_amount"></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Update Purchase</button>
                                </div>
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
        let purchaseData = null;

        function updateTotalAmount() {
    let subtotal = 0;
    let totalOrderTax = 0;
    let totalOrderDiscount = 0;

    document.querySelectorAll('#items-table tbody tr').forEach(row => {
        const qtyInput = row.querySelector('.quantity');
        const priceInput = row.querySelector('.purchase_price');
        const productInput = row.querySelector('.product_id');

        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value);
        const discount = parseFloat(row.querySelector('.discount').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;

        // Error divs
        const priceError = row.querySelector('.error-purchase_price');
        const qtyError = row.querySelector('.error-quantity');
        const productError = row.querySelector('.error-product_id');

        // Clear existing error messages
        priceError.innerText = '';
        qtyError.innerText = '';
        productError.innerText = '';

        // Show validation messages
        if (!productInput.value) {
            productError.innerText = 'Please select a product';
        }

        if (qty <= 0) {
            qtyError.innerText = 'Quantity is required';
        }

        if (price <= 1) {
            priceError.innerText = 'Purchase price is required';
        }

        const itemDiscount = discount * qty;
        const taxAmount = ((price - discount) * tax / 100) * qty;
        const unitCost = (price - discount) + ((price - discount) * tax / 100);
        const totalCost = unitCost * qty;

        row.querySelector('.tax_amount').value = taxAmount.toFixed(2);
        row.querySelector('.unit_cost').value = unitCost.toFixed(2);
        row.querySelector('.total_cost').value = totalCost.toFixed(2);

        subtotal += totalCost;
        totalOrderTax += taxAmount;
        totalOrderDiscount += itemDiscount;
    });

    const shipping = parseFloat(document.getElementById('shipping').value) || 0;
    const total = subtotal + shipping;

    document.getElementById('order_discount').value = totalOrderDiscount.toFixed(2);
    document.getElementById('order_tax').value = totalOrderTax.toFixed(2);
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
                    <div class="text-danger error-product_id"></div>
                </td>
                <td>
                    <input type="number" name="items[][quantity]" class="form-control quantity" min="1" value="${item?.quantity || 1}">
                    <div class="text-danger error-quantity"></div>
                </td>
                <td>
                    <input type="number" name="items[][purchase_price]" class="form-control purchase_price" step="0.01" value="${item?.purchase_price}">
                    <div class="text-danger error-purchase_price"></div>
                </td>
                <td>
                    <input type="number" name="items[][discount]" class="form-control discount" step="0.01" value="${item?.discount || 0}">
                    <div class="text-danger error-discount"></div>
                </td>
                <td>
                    <input type="number" name="items[][tax]" class="form-control tax" step="0.01" value="${item?.tax || item?.tax_percent || 0}">
                    <div class="text-danger error-tax"></div>
                </td>
                <td><input type="number" name="items[][tax_amount]" class="form-control tax_amount" step="0.01" readonly value="${item?.tax_amount || 0}"></td>
                <td><input type="number" name="items[][unit_cost]" class="form-control unit_cost" step="0.01" readonly value="${item?.unit_cost || 0}"></td>
                <td><input type="number" name="items[][total_cost]" class="form-control total_cost" step="0.01" readonly value="${item?.total_cost || 0}"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button></td>
            `;

            tbody.appendChild(row);

            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', updateTotalAmount);
            });

            row.querySelector('.remove-item-btn').addEventListener('click', () => {
                row.remove();
                updateTotalAmount();
            });

            updateTotalAmount();
        }

        async function populateDropdown(url, elementId, selectedId = null) {
            const res = await fetch(url, { credentials: 'include' });
            const data = await res.json();
            const select = document.getElementById(elementId);
            select.innerHTML = `<option disabled selected>Select ${elementId.replace('_id', '')}</option>`;
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.text = item.name;
                if (selectedId && item.id == selectedId) opt.selected = true;
                select.appendChild(opt);
            });
        }

        async function loadProducts() {
            const res = await fetch('http://127.0.0.1:8000/api/products', {
                credentials: 'include'
            });
            productsList = await res.json();
        }

        async function loadPurchase() {
            const res = await fetch(`http://127.0.0.1:8000/api/purchases/${purchaseId}`, {
                credentials: 'include'
            });
            purchaseData = await res.json();
        }

        async function prefillForm() {
            if (!purchaseData) return;

            document.getElementById('purchase_date').value = new Date(purchaseData.purchase_date).toISOString().split('T')[0];
            document.getElementById('order_discount').value = purchaseData.order_discount || 0;
            document.getElementById('order_tax').value = purchaseData.order_tax || 0;
            document.getElementById('shipping').value = purchaseData.shipping || 0;
            document.getElementById('status').value = purchaseData.status;
            document.getElementById('payment_status').value = purchaseData.payment_status;

            await Promise.all([
                populateDropdown('http://127.0.0.1:8000/api/stores', 'store_id', purchaseData.store_id),
                populateDropdown('http://127.0.0.1:8000/api/vendors', 'vendor_id', purchaseData.vendor_id)
            ]);

            purchaseData.items.forEach(item => addItemRow(item));
        }

        function clearValidationErrors() {
            document.querySelectorAll('.text-danger').forEach(el => el.innerText = '');
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

        document.addEventListener('DOMContentLoaded', async () => {
            await loadProducts();
            await loadPurchase();
            await prefillForm();

            ['shipping'].forEach(id => {
                document.getElementById(id).addEventListener('input', updateTotalAmount);
            });

            document.getElementById('add-item-btn').addEventListener('click', () => addItemRow());

            document.getElementById('edit-purchase-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                clearValidationErrors();

                const data = {
                    store_id: document.getElementById('store_id').value,
                    vendor_id: document.getElementById('vendor_id').value,
                    purchase_date: document.getElementById('purchase_date').value,
                    order_discount: document.getElementById('order_discount').value,
                    order_tax: document.getElementById('order_tax').value,
                    shipping: document.getElementById('shipping').value,
                    status: document.getElementById('status').value,
                    payment_status: document.getElementById('payment_status').value,
                    total_amount: document.getElementById('total_amount').value,
                    items: []
                };

                document.querySelectorAll('#items-table tbody tr').forEach(row => {
                    data.items.push({
                        product_id: row.querySelector('.product_id').value,
                        quantity: parseFloat(row.querySelector('.quantity').value),
                        purchase_price: parseFloat(row.querySelector('.purchase_price').value),
                        discount: parseFloat(row.querySelector('.discount').value),
                        tax: parseFloat(row.querySelector('.tax').value),
                        tax_amount: parseFloat(row.querySelector('.tax_amount').value),
                        unit_cost: parseFloat(row.querySelector('.unit_cost').value),
                        total_cost: parseFloat(row.querySelector('.total_cost').value),
                    });
                });

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
                    displayValidationErrors(result.errors);
                } else if (response.ok) {
                    window.location.href = "{{ route('purchases.index') }}";
                } else {
                    alert('Error: ' + (result.message || 'Something went wrong.'));
                }
            });
        });
    </script>
@endsection


