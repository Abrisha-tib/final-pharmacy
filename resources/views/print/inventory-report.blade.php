<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        
        .print-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #2E86AB;
            padding-bottom: 8px;
        }
        
        .header h1 {
            color: #2E86AB;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 12px;
        }
        
        
        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .medicines-table th {
            background: #2E86AB;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
        }
        
        .medicines-table td {
            padding: 6px 6px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        
        .medicines-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .medicines-table tr:hover {
            background: #e3f2fd;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-in-stock {
            background: #d4edda;
            color: #155724;
        }
        
        .status-low-stock {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        
        .expiry-warning {
            color: #dc3545;
            font-weight: bold;
        }
        
        .expiry-soon {
            color: #ffc107;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: #666;
            font-size: 12px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                padding: 0;
            }
            
            .medicines-table {
                page-break-inside: avoid;
            }
            
            .medicines-table tr {
                page-break-inside: avoid;
            }
        }
        
        @page {
            margin: 0.3cm;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">
            <h1>Pharmacy Inventory Report</h1>
            <div class="subtitle">Generated on {{ now()->format('F d, Y H:i A') }}</div>
        </div>


        <!-- Medicines Table -->
        @if($medicines->count() > 0)
            <table class="medicines-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Category</th>
                        <th>Batch Number</th>
                        <th>Stock Qty</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Expiry Date</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicines as $index => $medicine)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->generic_name }}</td>
                            <td>{{ $medicine->category->name ?? 'N/A' }}</td>
                            <td>{{ $medicine->batch_number }}</td>
                            <td>{{ $medicine->stock_quantity }}</td>
                            <td>
                                <span class="status-badge status-{{ $medicine->stock_status }}">
                                    {{ str_replace('_', ' ', ucfirst($medicine->stock_status)) }}
                                </span>
                            </td>
                            <td>{{ number_format($medicine->selling_price, 2) }} Birr</td>
                            <td>
                                @if($medicine->is_expired)
                                    <span class="expiry-warning">{{ $medicine->expiry_date->format('Y-m-d') }} (Expired)</span>
                                @elseif($medicine->days_until_expiry <= 30)
                                    <span class="expiry-soon">{{ $medicine->expiry_date->format('Y-m-d') }} (Exp. Soon)</span>
                                @else
                                    {{ $medicine->expiry_date->format('Y-m-d') }}
                                @endif
                            </td>
                            <td>{{ number_format($medicine->selling_price * $medicine->stock_quantity, 2) }} Birr</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>No medicines found for the selected filters.</h3>
            </div>
        @endif

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} Pharmacy. All rights reserved.</p>
            <p>Generated on {{ now()->format('F d, Y H:i A') }} | Page 1 of 1</p>
        </div>
    </div>

    <script>
        // Automatically trigger print dialog when the page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>