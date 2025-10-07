<!-- Printer Settings Modal -->
<div id="printerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Printer Settings</h3>
            </div>
            <div class="p-6">
                <form id="printerForm">
                    <!-- Printer Configuration Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Printer Configuration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Printer</label>
                                <select name="default_printer" id="default_printer" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Default Printer</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Print Quality</label>
                                <select name="print_quality" id="print_quality" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="draft">Draft</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Paper Size</label>
                                <select name="paper_size" id="paper_size" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="A4" selected>A4</option>
                                    <option value="A3">A3</option>
                                    <option value="Letter">Letter</option>
                                    <option value="Legal">Legal</option>
                                    <option value="A5">A5</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Orientation</label>
                                <select name="orientation" id="orientation" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="portrait" selected>Portrait</option>
                                    <option value="landscape">Landscape</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color Mode</label>
                                <select name="color_mode" id="color_mode" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="color" selected>Color</option>
                                    <option value="grayscale">Grayscale</option>
                                    <option value="black">Black & White</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Print Margins</label>
                                <select name="print_margin" id="print_margin" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="minimal">Minimal</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="wide">Wide</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Options Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Advanced Options</h4>
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="duplex" id="duplex" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Duplex Printing (Double-sided)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="auto_cut" id="auto_cut" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Auto Cut After Printing</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="header_footer" id="header_footer" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500" checked>
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Print Headers and Footers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="watermark" id="watermark" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Print Watermark</span>
                            </label>
                        </div>
                    </div>

                    <!-- Available Printers Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Printers</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div id="availablePrinters" class="space-y-2">
                                <!-- Available printers will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Test Printer Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Test Printer</h4>
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Test your printer configuration by sending a test page.
                            </p>
                            <button type="button" onclick="testPrinter()" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-print mr-2"></i>
                                Print Test Page
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closePrinterModal()" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button onclick="savePrinterSettings()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load printer settings
function loadPrinterSettings() {
    fetch('/settings/printer', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const settings = data.settings;
            
            // Basic settings
            document.getElementById('default_printer').value = settings.default_printer;
            document.getElementById('print_quality').value = settings.print_quality;
            document.getElementById('paper_size').value = settings.paper_size;
            document.getElementById('orientation').value = settings.orientation;
            document.getElementById('color_mode').value = settings.color_mode;
            document.getElementById('print_margin').value = settings.print_margin;
            
            // Advanced options
            document.getElementById('duplex').checked = settings.duplex;
            document.getElementById('auto_cut').checked = settings.auto_cut;
            document.getElementById('header_footer').checked = settings.header_footer;
            document.getElementById('watermark').checked = settings.watermark;
        }
    })
    .catch(error => {
        console.error('Error loading printer settings:', error);
    });

    // Load available printers
    loadAvailablePrinters();
}

// Load available printers
function loadAvailablePrinters() {
    fetch('/settings/printer/available', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const printers = data.printers;
            const container = document.getElementById('availablePrinters');
            container.innerHTML = '';
            
            if (printers.length > 0) {
                printers.forEach(printer => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500';
                    div.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-print text-blue-600 dark:text-blue-400 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">${printer.name}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">${printer.type} • ${printer.status}</div>
                            </div>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${printer.status === 'available' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'}">
                            ${printer.status}
                        </span>
                    `;
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">No printers found</p>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading available printers:', error);
    });
}

// Save printer settings
function savePrinterSettings() {
    const formData = new FormData(document.getElementById('printerForm'));
    const data = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        data[key] = value === 'on' ? true : value;
    }
    
    fetch('/settings/printer', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Printer settings updated successfully!');
            closePrinterModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving printer settings.');
    });
}

// Test printer
function testPrinter() {
    const defaultPrinter = document.getElementById('default_printer').value;
    
    if (!defaultPrinter) {
        alert('Please select a default printer first.');
        return;
    }
    
    fetch('/settings/printer/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ printer: defaultPrinter })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test page sent to printer successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while testing printer.');
    });
}

// Close printer modal
function closePrinterModal() {
    document.getElementById('printerModal').classList.add('hidden');
}

// Open printer modal
function openPrinterModal() {
    loadPrinterSettings();
    document.getElementById('printerModal').classList.remove('hidden');
}
</script>
