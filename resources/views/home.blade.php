<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Pharmacy System') }} - Dashboard</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('analo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('analo.png') }}">
    <link rel="shortcut icon" href="{{ asset('analo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('analo.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-pills me-2"></i>Pharmacy System
            </a>
            
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, {{ Auth::user()->name }}!
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline" id="logoutForm">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm" id="logoutBtn">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-boxes fa-2x mb-2"></i>
                                        <h5>Inventory</h5>
                                        <p class="mb-0">Manage Products</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-cash-register fa-2x mb-2"></i>
                                        <h5>Sales</h5>
                                        <p class="mb-0">Process Sales</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                        <h5>Purchases</h5>
                                        <p class="mb-0">Manage Orders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                        <h5>Reports</h5>
                                        <p class="mb-0">View Analytics</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Welcome to Pharmacy Management System!
                            </h5>
                            <p class="mb-0">You are successfully logged in. The system is ready for use.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Debug logout functionality
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            console.log('Logout button clicked');
            console.log('Form action:', document.getElementById('logoutForm').action);
            console.log('CSRF token:', document.querySelector('input[name="_token"]').value);
        });
        
        document.getElementById('logoutForm').addEventListener('submit', function(e) {
            console.log('Logout form submitted');
        });
    </script>
</body>
</html>
