@extends('dashboard.partials.main')
@section('css')
@endsection

@section('content')
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Add User</h5>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter full name" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter email" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter password" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="" disabled selected>Select role</option>
                                        <option value="admin">Admin</option>
                                        <option value="cashier">Cashier</option>
                                        <option value="manager">Manager</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="" disabled selected>Select status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Create User</button>
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
@endsection