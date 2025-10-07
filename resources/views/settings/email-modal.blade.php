<!-- Email Settings Modal -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email Settings</h3>
            </div>
            <div class="p-6">
                <form id="emailForm">
                    <!-- SMTP Configuration Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SMTP Configuration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Host</label>
                                <input type="text" name="smtp[host]" id="smtp_host" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="smtp.gmail.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Port</label>
                                <input type="number" name="smtp[port]" id="smtp_port" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="587" value="587">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                                <input type="text" name="smtp[username]" id="smtp_username" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="your-email@gmail.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                                <input type="password" name="smtp[password]" id="smtp_password" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="Your email password">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Encryption</label>
                                <select name="smtp[encryption]" id="smtp_encryption" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Address</label>
                                <input type="email" name="smtp[from_address]" id="smtp_from_address" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="noreply@pharmacy.com">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Name</label>
                                <input type="text" name="smtp[from_name]" id="smtp_from_name" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="Pharmacy Management System">
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Settings</h4>
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="notifications[enabled]" id="notifications_enabled" 
                                       class="w-4 h-4 text-indigo-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Email Notifications</span>
                            </label>
                            <div class="ml-6 space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="notifications[low_stock]" id="notifications_low_stock" 
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500" checked>
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Low Stock Alerts</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="notifications[system_alerts]" id="notifications_system_alerts" 
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500" checked>
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">System Alerts</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="notifications[user_activities]" id="notifications_user_activities" 
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">User Activity Notifications</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Queue Settings Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Queue Settings</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center mb-4">
                                    <input type="checkbox" name="queue[enabled]" id="queue_enabled" 
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Email Queue</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Queue Connection</label>
                                <select name="queue[connection]" id="queue_connection" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="database">Database</option>
                                    <option value="sync">Synchronous</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Test Email Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Test Configuration</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Test your email configuration by sending a test email to your account.
                            </p>
                            <button type="button" onclick="testEmailConfiguration()" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Test Email
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeEmailModal()" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button onclick="saveEmailSettings()" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load email settings
function loadEmailSettings() {
    fetch('/settings/email', {
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
            
            // SMTP Settings
            document.getElementById('smtp_host').value = settings.smtp.host;
            document.getElementById('smtp_port').value = settings.smtp.port;
            document.getElementById('smtp_username').value = settings.smtp.username;
            document.getElementById('smtp_password').value = settings.smtp.password;
            document.getElementById('smtp_encryption').value = settings.smtp.encryption;
            document.getElementById('smtp_from_address').value = settings.smtp.from_address;
            document.getElementById('smtp_from_name').value = settings.smtp.from_name;
            
            // Notification Settings
            document.getElementById('notifications_enabled').checked = settings.notifications.enabled;
            document.getElementById('notifications_low_stock').checked = settings.notifications.low_stock;
            document.getElementById('notifications_system_alerts').checked = settings.notifications.system_alerts;
            document.getElementById('notifications_user_activities').checked = settings.notifications.user_activities;
            
            // Queue Settings
            document.getElementById('queue_enabled').checked = settings.queue.enabled;
            document.getElementById('queue_connection').value = settings.queue.connection;
        }
    })
    .catch(error => {
        console.error('Error loading email settings:', error);
    });
}

// Save email settings
function saveEmailSettings() {
    const formData = new FormData(document.getElementById('emailForm'));
    const data = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        const keys = key.split('[').map(k => k.replace(']', ''));
        if (keys.length === 2) {
            if (!data[keys[0]]) data[keys[0]] = {};
            data[keys[0]][keys[1]] = value === 'on' ? true : value;
        }
    }
    
    fetch('/settings/email', {
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
            alert('Email settings updated successfully!');
            closeEmailModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving email settings.');
    });
}

// Test email configuration
function testEmailConfiguration() {
    fetch('/settings/email/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test email sent successfully! Check your inbox.');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while testing email configuration.');
    });
}

// Close email modal
function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
}

// Open email modal
function openEmailModal() {
    loadEmailSettings();
    document.getElementById('emailModal').classList.remove('hidden');
}
</script>
