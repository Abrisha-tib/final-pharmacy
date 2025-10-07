@extends('layouts.app')

@section('title', 'Contact Support - Help & Support')
@section('page-title', 'Contact Support')
@section('page-description', 'Get in touch with our support team')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Contact Support</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Get direct help from our support team</p>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Analog Software Solution - Arba Minch, Ethiopia</p>
                </div>
                <div class="text-right">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact Form -->
        <div class="lg:col-span-2">
            <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Send us a message</h2>
                
                <form id="supportForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                            <input type="text" name="subject" required class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Brief description of your issue">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                            <select name="category" required class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Select category</option>
                                <option value="technical">Technical Issue</option>
                                <option value="billing">Billing Question</option>
                                <option value="feature_request">Feature Request</option>
                                <option value="bug_report">Bug Report</option>
                                <option value="general">General Question</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                            <select name="priority" required class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Select priority</option>
                                <option value="low">Low - General question</option>
                                <option value="medium">Medium - Minor issue</option>
                                <option value="high">High - Important issue</option>
                                <option value="urgent">Urgent - Critical issue</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Attachments</label>
                            <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 3 files, 5MB each (PDF, DOC, DOCX, JPG, PNG)</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                        <textarea name="message" required rows="6" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Please provide detailed information about your issue..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                        <button type="button" id="clearForm" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-undo mr-2"></i>
                            Clear Form
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="space-y-6">
            <!-- Contact Details -->
            <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Email Support</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">abst738@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Phone Support</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">0955 88 67 09</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">0915 51 27 07</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Location</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Arba Minch, Ethiopia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Times -->
            <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Response Times</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Urgent</span>
                        <span class="text-sm font-semibold text-red-500">2 hours</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">High</span>
                        <span class="text-sm font-semibold text-orange-500">4 hours</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Medium</span>
                        <span class="text-sm font-semibold text-yellow-500">8 hours</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Low</span>
                        <span class="text-sm font-semibold text-green-500">24 hours</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Links</h3>
                <div class="space-y-3">
                    <a href="{{ route('help-support.faq') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-question-circle w-5 h-5 text-blue-500 mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300">FAQ</span>
                    </a>
                    <a href="{{ route('help-support.documentation') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-book w-5 h-5 text-green-500 mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300">Documentation</span>
                    </a>
                    <a href="{{ route('help-support.index') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-home w-5 h-5 text-purple-500 mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300">Help Center</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('supportForm');
    const clearBtn = document.getElementById('clearForm');

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        submitBtn.disabled = true;

        fetch('/help-support/submit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Support request submitted successfully! We will get back to you within 24 hours.', 'success');
                form.reset();
            } else {
                showNotification(data.message || 'Failed to submit support request', 'error');
            }
        })
        .catch(error => {
            showNotification('An error occurred while submitting your request', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Handle clear form
    clearBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the form?')) {
            form.reset();
        }
    });

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                ${message}
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
</script>
@endsection
