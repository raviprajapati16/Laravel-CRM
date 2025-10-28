<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Contacts</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --info-color: #4895ef;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #4a5568;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: var(--box-shadow);
            position: fixed;
            width: 250px;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand {
            padding: 20px 0 30px;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand i {
            color: #4cc9f0;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: var(--transition);
        }

        .page-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-title {
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            font-size: 1.8rem;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 24px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #3aa8d0;
            border-color: #3aa8d0;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #e11570;
            border-color: #e11570;
            transform: translateY(-2px);
        }

        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        .btn-info:hover {
            background-color: #3a7bc8;
            border-color: #3a7bc8;
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-warning:hover {
            background-color: #e0861b;
            border-color: #e0861b;
            transform: translateY(-2px);
        }

        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table thead th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            color: #4a5568;
            padding: 16px 12px;
        }

        .table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(120deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close-white {
            filter: invert(1);
        }

        .badge {
            border-radius: 6px;
            padding: 6px 10px;
            font-weight: 500;
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            border-radius: 8px;
            margin: 0 4px;
            border: 1px solid #e2e8f0;
            color: var(--primary-color);
            font-weight: 500;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .merged-contact {
            font-size: 0.85em;
            color: #718096;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            align-items: center;
        }

        .merged-contact img {
            margin-right: 8px;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--box-shadow);
        }

        .stats-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .stats-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .filter-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 24px;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar .nav-link span {
                display: none;
            }

            .sidebar-brand span {
                display: none;
            }

            .sidebar-brand i {
                margin-right: 0;
                font-size: 1.8rem;
            }

            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="sidebar-brand">
                    <i class="fas fa-address-book"></i>
                    <span>CRM Pro</span>
                </div>
                <div class="nav flex-column px-3">
                    <a href="{{ route('contacts.index') }}" 
                       class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i>
                        <span>Contacts</span>
                    </a>
                    <a href="{{ route('custom-fields.index') }}" 
                       class="nav-link {{ request()->routeIs('custom-fields.*') ? 'active' : '' }}">
                        <i class="fas fa-cogs me-2"></i>
                        <span>Custom Fields</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
</body>

</html>