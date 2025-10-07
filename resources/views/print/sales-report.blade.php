<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - {{ date('Y-m-d H:i:s') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item h3 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        
        .summary-item p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 11px;
        }
        
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 5px;
        }
        
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #495057;
        }
        
        .filters p {
            margin: 2px 0;
            font-size: 11px;
            color: #6c757d;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        
        .payment-cash {
            color: #17a2b8;
        }
        
        .payment-card {
            color: #6f42c1;
        }
        
        .payment-mobile {
            color: #20c997;
        }
        
        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            color: #666;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
        <p>Analog Pharmacy Management System</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <h3>{{ $totalSales }}</h3>
            <p>Total Sales</p>
        </div>
        <div class="summary-item">
            <h3>{{ $completedSales }}</h3>
            <p>Completed Sales</p>
        </div>
        <div class="summary-item">
            <h3>{{ $pendingSales }}</h3>
            <p>Pending Sales</p>
        </div>
        <div class="summary-item">
            <h3>Br {{ number_format($totalRevenue, 2) }}</h3>
            <p>Total Revenue</p>
        </div>
    </div>

    @if(!empty($filters) && array_filter($filters))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['status']) && $filters['status'])
            <p><strong>Status:</strong> {{ ucfirst($filters['status']) }}</p>
        @endif
        @if(isset($filters['payment_method']) && $filters['payment_method'])
            <p><strong>Payment Method:</strong> {{ ucfirst($filters['payment_method']) }}</p>
        @endif
        @if(isset($filters['date_from']) && $filters['date_from'])
            <p><strong>Date From:</strong> {{ date('F j, Y', strtotime($filters['date_from'])) }}</p>
        @endif
        @if(isset($filters['date_to']) && $filters['date_to'])
            <p><strong>Date To:</strong> {{ date('F j, Y', strtotime($filters['date_to'])) }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Sold By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale['id'] }}</td>
                <td>{{ $sale['customer_name'] }}</td>
                <td>
                    @foreach($sale['items'] as $item)
                        {{ $item['name'] }} (x{{ $item['quantity'] }})<br>
                    @endforeach
                </td>
                <td class="payment-{{ strtolower(str_replace(' ', '-', $sale['payment_method'])) }}">
                    {{ $sale['payment_method'] }}
                </td>
                <td class="status-{{ $sale['status'] }}">
                    {{ ucfirst($sale['status']) }}
                </td>
                <td>Br {{ number_format($sale['total_amount'], 2) }}</td>
                <td>{{ date('M j, Y', strtotime($sale['sale_date'])) }}</td>
                <td>{{ $sale['sold_by'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5"><strong>Total Revenue:</strong></td>
                <td><strong>Br {{ number_format($totalRevenue, 2) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This report was generated by Analog Pharmacy Management System</p>
        <p>Report generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
