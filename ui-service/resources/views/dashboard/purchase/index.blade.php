@extends('dashboard.partials.main')

@section('css')
@endsection

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">

        <!-- Success Message Alert -->
        <div id="success-message" class="alert alert-success d-none" role="alert">
            <span id="success-text"></span>
        </div>

        <div class="buttons text-end mb-3">
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">Add Purchase</a>
        </div>

        <!-- Row 1 -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-md-flex align-items-center justify-content-between">
                            <h4 class="card-title">Purchases</h4>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table">
                                    <tr>
                                        <th>ID</th>
                                        <th>Store</th>
                                        <th>User</th>
                                        <th>Vendor</th>
                                        <th>Purchase Date</th>
                                        <th>Total Amount</th>
                                        <th>Products</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="purchases-table-body">
                                    <tr>
                                        <td colspan="8">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
        const tbody = document.getElementById('purchases-table-body');
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');

        function showSuccess(message) {
            successText.textContent = message;
            successMessage.classList.remove('d-none');
            successMessage.scrollIntoView({ behavior: 'smooth' });

            setTimeout(() => {
                successMessage.classList.add('d-none');
                successText.textContent = '';
            }, 3000);
        }

        function loadPurchases() {
            fetch('http://127.0.0.1:8000/api/purchases', {
                method: 'GET',
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                tbody.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8">No purchases found.</td></tr>';
                    return;
                }

                data.forEach(purchase => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${purchase.id}</td>
                        <td>${purchase.store?.name || 'N/A'}</td>
                        <td>${purchase.user?.name || 'N/A'}</td>
                        <td>${purchase.vendor?.name || 'N/A'}</td>
                        <td>${purchase.purchase_date || 'N/A'}</td>
                        <td>${purchase.total_amount || 0}</td>
                        <td>
                            ${purchase.items?.map(item => item.product?.id).filter(Boolean).join(', ') || 'N/A'}
                        </td>
                        <td>
                            <a href="/purchases/edit/${purchase.id}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="deletePurchase(${purchase.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                tbody.innerHTML = `<tr><td colspan="8" class="text-danger">Error fetching purchases: ${error.message}</td></tr>`;
                console.error('Error fetching purchases:', error);
            });
        }

        window.deletePurchase = function (purchaseId) {
            fetch(`http://127.0.0.1:8000/api/purchases/${purchaseId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                },
                credentials: 'include'
            })
            .then(response => response.json()
                .then(data => ({
                    status: response.status,
                    body: data
                })))
            .then(({ status, body }) => {
                if (status === 200) {
                    loadPurchases();
                    showSuccess('Purchase deleted successfully!');
                } else {
                    alert('Error: ' + (body.message || 'Failed to delete purchase'));
                }
            })
            .catch(error => {
                console.error('Error deleting purchase:', error);
            });
        };

        loadPurchases();
    });
</script>
@endsection
