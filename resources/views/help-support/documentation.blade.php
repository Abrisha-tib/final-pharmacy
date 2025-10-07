@extends('layouts.app')

@section('title', 'Documentation - Help & Support')
@section('page-title', 'Documentation')
@section('page-description', 'Complete user guide and documentation')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Documentation</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Complete guide to using the pharmacy management system</p>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Analog Software Solution - Arba Minch, Ethiopia</p>
                </div>
                <div class="text-right">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($documentation as $section)
        <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
            <div class="flex items-start mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-4 flex-shrink-0">
                    <i class="fas fa-book-open text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $section['title'] }}</h2>
                    <p class="text-gray-600 dark:text-gray-300">{{ $section['description'] }}</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($section['sections'] as $subsection)
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-file-alt text-blue-500 mr-3"></i>
                    <span class="text-gray-700 dark:text-gray-300">{{ $subsection }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Start Guide -->
    <div class="mt-12">
        <div class="card-hover bg-gradient-to-br from-white to-green-50 dark:from-gray-800 dark:to-green-900/20 rounded-2xl shadow-lg border border-green-100 dark:border-green-800 p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                    <i class="fas fa-rocket text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Quick Start Guide</h2>
                    <p class="text-gray-600 dark:text-gray-300">Get up and running in minutes</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <span class="text-white font-bold text-xl">1</span>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Setup</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Configure your pharmacy settings and add your first medicine</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <span class="text-white font-bold text-xl">2</span>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Inventory</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Add medicines, set stock levels, and configure alerts</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <span class="text-white font-bold text-xl">3</span>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Sales</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Process sales, generate receipts, and manage transactions</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <span class="text-white font-bold text-xl">4</span>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Reports</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Generate reports and analyze your business performance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Tutorials -->
    <div class="mt-12">
        <div class="card-hover bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 rounded-2xl shadow-lg border border-purple-100 dark:border-purple-800 p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                    <i class="fas fa-play-circle text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Video Tutorials</h2>
                    <p class="text-gray-600 dark:text-gray-300">Watch step-by-step video guides</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <i class="fas fa-play text-white text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Getting Started</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Learn the basics of the system</p>
                    <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200">
                        Watch Now
                    </button>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <i class="fas fa-play text-white text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Inventory Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Managing your pharmacy inventory</p>
                    <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200">
                        Watch Now
                    </button>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <i class="fas fa-play text-white text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Sales Processing</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">How to process sales and transactions</p>
                    <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200">
                        Watch Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Resources -->
    <div class="mt-12">
        <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                    <i class="fas fa-external-link-alt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Additional Resources</h2>
                    <p class="text-gray-600 dark:text-gray-300">More helpful resources and links</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Downloadable Guides</h3>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-download text-blue-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">User Manual (PDF)</span>
                        </a>
                        <a href="#" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-download text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Quick Reference Card</span>
                        </a>
                        <a href="#" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-download text-purple-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Keyboard Shortcuts</span>
                        </a>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Community</h3>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-users text-blue-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">User Forum</span>
                        </a>
                        <a href="#" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-comments text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Discussion Board</span>
                        </a>
                        <a href="#" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Feature Requests</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
