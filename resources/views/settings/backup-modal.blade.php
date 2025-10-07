<!-- Backup & Restore Modal -->
<div id="backupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Backup & Restore</h3>
            </div>
            <div class="p-6">
                <!-- Create Backup Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Backup</h4>
                    <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                        <form id="createBackupForm" class="flex items-end space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Backup Name (Optional)</label>
                                <input type="text" name="name" id="backup_name" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white" 
                                       placeholder="Enter backup name or leave empty for auto-generated name">
                            </div>
                            <button type="button" onclick="createBackup()" 
                                    class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>
                                Create Backup
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Backup Statistics -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Backup Statistics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white" id="totalBackups">-</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Backups</div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="completedBackups">-</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="failedBackups">-</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Failed</div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="text-center">
                                <div class="text-sm font-bold text-gray-900 dark:text-white" id="lastBackupDate">-</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Last Backup</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup History -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Backup History</h4>
                    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-600">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="backupTableBody" class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                    <!-- Backup rows will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeBackupModal()" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load backup data
function loadBackupData() {
    // Load backup statistics
    fetch('/settings/backups/stats', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.stats;
            document.getElementById('totalBackups').textContent = stats.total;
            document.getElementById('completedBackups').textContent = stats.completed;
            document.getElementById('failedBackups').textContent = stats.failed;
            document.getElementById('lastBackupDate').textContent = stats.last_backup_date;
        }
    })
    .catch(error => {
        console.error('Error loading backup stats:', error);
    });

    // Load backup list
    fetch('/settings/backups', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('backupTableBody');
            tbody.innerHTML = '';
            
            if (data.backups.data && data.backups.data.length > 0) {
                data.backups.data.forEach(backup => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${backup.name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 capitalize">${backup.type}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${backup.formatted_size || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${backup.status_badge_class}">
                                ${backup.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${new Date(backup.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            ${backup.status === 'completed' ? `
                                <button onclick="downloadBackup(${backup.id})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <i class="fas fa-download"></i>
                                </button>
                            ` : ''}
                            <button onclick="deleteBackup(${backup.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No backups found</td></tr>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading backups:', error);
    });
}

// Create backup
function createBackup() {
    const formData = new FormData(document.getElementById('createBackupForm'));
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    fetch('/settings/backups', {
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
            alert('Backup created successfully!');
            loadBackupData(); // Refresh the data
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating backup.');
    });
}

// Download backup
function downloadBackup(backupId) {
    window.open(`/settings/backups/${backupId}/download`, '_blank');
}

// Delete backup
function deleteBackup(backupId) {
    if (confirm('Are you sure you want to delete this backup?')) {
        fetch(`/settings/backups/${backupId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Backup deleted successfully!');
                loadBackupData(); // Refresh the data
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting backup.');
        });
    }
}

// Close backup modal
function closeBackupModal() {
    document.getElementById('backupModal').classList.add('hidden');
}

// Open backup modal
function openBackupModal() {
    loadBackupData();
    document.getElementById('backupModal').classList.remove('hidden');
}
</script>
