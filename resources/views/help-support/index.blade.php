@extends('layouts.app')

@section('title', 'Help & Support - Analog Pharmacy Management System')
@section('page-title', 'Help & Support')
@section('page-description', 'Get help and support for your pharmacy management system')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Help & Support</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Get assistance and learn how to use the system effectively</p>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Analog Software Solution - Arba Minch, Ethiopia</p>
                </div>
                <div class="text-right">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-question-circle text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('help-support.faq') }}" class="card-hover bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-2xl shadow-lg border border-blue-100 dark:border-blue-800 p-6 text-center transition-all duration-300 hover:-translate-y-1">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-question-circle text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">FAQ</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Frequently asked questions</p>
        </a>

        <a href="{{ route('help-support.documentation') }}" class="card-hover bg-gradient-to-br from-white to-green-50 dark:from-gray-800 dark:to-green-900/20 rounded-2xl shadow-lg border border-green-100 dark:border-green-800 p-6 text-center transition-all duration-300 hover:-translate-y-1">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-book text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Documentation</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Complete user guide</p>
        </a>

        <a href="{{ route('help-support.contact') }}" class="card-hover bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 rounded-2xl shadow-lg border border-purple-100 dark:border-purple-800 p-6 text-center transition-all duration-300 hover:-translate-y-1">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-envelope text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Contact Support</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Get direct help</p>
        </a>

        <a href="{{ route('user-preferences.index') }}" class="card-hover bg-gradient-to-br from-white to-orange-50 dark:from-gray-800 dark:to-orange-900/20 rounded-2xl shadow-lg border border-orange-100 dark:border-orange-800 p-6 text-center transition-all duration-300 hover:-translate-y-1">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-cog text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Preferences</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Customize settings</p>
        </a>
    </div>

    <!-- Help Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($helpSections as $section)
        <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
            <div class="flex items-start mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-4 flex-shrink-0">
                    <i class="{{ $section['icon'] }} text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $section['title'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-300">{{ $section['description'] }}</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($section['items'] as $item)
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-chevron-right text-blue-500 mr-3"></i>
                    <span class="text-gray-700 dark:text-gray-300">{{ $item }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Contact Information -->
    <div class="mt-8">
        <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-6">
                    <i class="fas fa-headset text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Need More Help?</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Our support team is here to help you succeed</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-3">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Email Support</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">abst738@gmail.com</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-3">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Phone Support</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">0955 88 67 09</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">0915 51 27 07</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-3">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Location</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Arba Minch, Ethiopia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
