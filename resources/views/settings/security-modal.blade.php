<!-- Security Settings Modal -->
<div id="securityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Security Settings</h3>
            </div>
            <div class="p-6">
                <form id="securityForm">
                    <!-- Password Policy Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Password Policy</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Length</label>
                                <input type="number" name="password_policy[min_length]" id="password_min_length" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white" 
                                       min="6" max="50" value="8">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Age (Days)</label>
                                <input type="number" name="password_policy[max_age_days]" id="password_max_age_days" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white" 
                                       min="30" max="365" value="90">
                            </div>
                            <div class="md:col-span-2">
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="password_policy[require_uppercase]" id="password_require_uppercase" 
                                               class="w-4 h-4 text-red-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500" checked>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Uppercase Letters</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="password_policy[require_lowercase]" id="password_require_lowercase" 
                                               class="w-4 h-4 text-red-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500" checked>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Lowercase Letters</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="password_policy[require_numbers]" id="password_require_numbers" 
                                               class="w-4 h-4 text-red-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500" checked>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Numbers</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="password_policy[require_symbols]" id="password_require_symbols" 
                                               class="w-4 h-4 text-red-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Require Special Characters</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Locking Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Locking</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center mb-4">
                                    <input type="checkbox" name="account_locking[enabled]" id="account_locking_enabled" 
                                           class="w-4 h-4 text-red-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500" checked>
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Account Locking</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Attempts</label>
                                <input type="number" name="account_locking[max_attempts]" id="account_max_attempts" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white" 
                                       min="3" max="10" value="5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lockout Duration (Minutes)</label>
                                <input type="number" name="account_locking[lockout_duration]" id="account_lockout_duration" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white" 
                                       min="5" max="60" value="15">
                            </div>
                        </div>
                    </div>

                    <!-- Session Management Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Session Management</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center mb-4">
                                    <input type="checkbox" name="session_management[enabled]" id="session_management_enabled" 
                                           class="w-4 h-4 text-red-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500" checked>
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Session Management</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Timeout (Minutes)</label>
                                <input type="number" name="session_management[timeout]" id="session_timeout" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white" 
                                       min="15" max="480" value="120">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Concurrent Sessions</label>
                                <input type="number" name="session_management[max_concurrent]" id="session_max_concurrent" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white" 
                                       min="1" max="10" value="3">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeSecurityModal()" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button onclick="saveSecuritySettings()" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load security settings
function loadSecuritySettings() {
    fetch('/settings/security', {
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
            
            // Password Policy
            document.getElementById('password_min_length').value = settings.password_policy.min_length;
            document.getElementById('password_max_age_days').value = settings.password_policy.max_age_days;
            document.getElementById('password_require_uppercase').checked = settings.password_policy.require_uppercase;
            document.getElementById('password_require_lowercase').checked = settings.password_policy.require_lowercase;
            document.getElementById('password_require_numbers').checked = settings.password_policy.require_numbers;
            document.getElementById('password_require_symbols').checked = settings.password_policy.require_symbols;
            
            // Account Locking
            document.getElementById('account_locking_enabled').checked = settings.account_locking.enabled;
            document.getElementById('account_max_attempts').value = settings.account_locking.max_attempts;
            document.getElementById('account_lockout_duration').value = settings.account_locking.lockout_duration;
            
            // Session Management
            document.getElementById('session_management_enabled').checked = settings.session_management.enabled;
            document.getElementById('session_timeout').value = settings.session_management.timeout;
            document.getElementById('session_max_concurrent').value = settings.session_management.max_concurrent;
        }
    })
    .catch(error => {
        console.error('Error loading security settings:', error);
    });
}

// Save security settings
function saveSecuritySettings() {
    const formData = new FormData(document.getElementById('securityForm'));
    const data = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        const keys = key.split('[').map(k => k.replace(']', ''));
        if (keys.length === 2) {
            if (!data[keys[0]]) data[keys[0]] = {};
            data[keys[0]][keys[1]] = value === 'on' ? true : value;
        }
    }
    
    fetch('/settings/security', {
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
            alert('Security settings updated successfully!');
            closeSecurityModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving security settings.');
    });
}

// Close security modal
function closeSecurityModal() {
    document.getElementById('securityModal').classList.add('hidden');
}

// Open security modal
function openSecurityModal() {
    loadSecuritySettings();
    document.getElementById('securityModal').classList.remove('hidden');
}
</script>
