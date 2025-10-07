<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchase->purchase_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #10b981;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-section h3 {
            color: #10b981;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        .info-value {
            color: #6b7280;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }
        .items-table th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-section {
            text-align: right;
            border-top: 2px solid #10b981;
            padding-top: 20px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-received {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        @media print {
            body { background-color: white; }
            .container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Purchase Order</h1>
            <p>Analog Pharmacy Management System</p>
            <p>Purchase Number: {{ $purchase->purchase_number }}</p>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <h3>Purchase Information</h3>
                <div class="info-item">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">{{ $purchase->order_date->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Expected Delivery:</span>
                    <span class="info-value">{{ $purchase->expected_delivery ? $purchase->expected_delivery->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $purchase->status }}">{{ ucfirst($purchase->status) }}</span>
                    </span>
                </div>
                @if($purchase->delivery_date)
                <div class="info-item">
                    <span class="info-label">Delivery Date:</span>
                    <span class="info-value">{{ $purchase->delivery_date->format('M d, Y') }}</span>
                </div>
                @endif
            </div>

            <div class="info-section">
                <h3>Supplier Information</h3>
                <div class="info-item">
                    <span class="info-label">Supplier:</span>
                    <span class="info-value">{{ $purchase->supplier->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact Person:</span>
                    <span class="info-value">{{ $purchase->supplier->contact_person }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $purchase->supplier->phone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $purchase->supplier->email }}</span>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Batch Number</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->medicine->name }}</strong><br>
                        <small>{{ $item->medicine->generic_name }}</small>
                    </td>
                    <td>{{ $item->batch_number ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} Birr</td>
                    <td>{{ number_format($item->total_price, 2) }} Birr</td>
                    <td>{{ $item->expiry_date ? $item->expiry_date->format('M d, Y') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-amount">
                Total Amount: {{ number_format($purchase->total_amount, 2) }} Birr
            </div>
        </div>

        @if($purchase->notes)
        <div class="info-section">
            <h3>Notes</h3>
            <p>{{ $purchase->notes }}</p>
        </div>
        @endif

        <div style="margin-top: 40px; text-align: center; color: #6b7280; font-size: 12px;">
            <p>Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
            <p>Analog Pharmacy Management System</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
