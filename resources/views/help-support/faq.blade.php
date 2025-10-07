@extends('layouts.app')

@section('title', 'FAQ - Help & Support')
@section('page-title', 'Frequently Asked Questions')
@section('page-description', 'Find answers to common questions')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Frequently Asked Questions</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Find quick answers to common questions</p>
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

    <!-- Search Box -->
    <div class="mb-8">
        <div class="relative">
            <input type="text" id="faqSearch" placeholder="Search FAQ..." class="w-full px-6 py-4 pl-12 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="space-y-8">
        @php
        $categories = collect($faqs)->groupBy('category');
        @endphp

        @foreach($categories as $category => $categoryFaqs)
        <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                    <i class="fas fa-folder text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $category }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ count($categoryFaqs) }} questions</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($categoryFaqs as $index => $faq)
                <div class="faq-item border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                    <button class="faq-question w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex items-center justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 py-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $faq['answer'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Still Need Help? -->
    <div class="mt-12">
        <div class="card-hover bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-2xl shadow-lg border border-blue-100 dark:border-blue-800 p-8 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto mb-6">
                <i class="fas fa-headset text-white text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Still Need Help?</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Can't find what you're looking for? Our support team is here to help.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('help-support.contact') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Support
                </a>
                <a href="{{ route('help-support.documentation') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-book mr-2"></i>
                    View Documentation
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const icon = question.querySelector('i');
        
        question.addEventListener('click', function() {
            const isOpen = !answer.classList.contains('hidden');
            
            // Close all other FAQ items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    const otherAnswer = otherItem.querySelector('.faq-answer');
                    const otherIcon = otherItem.querySelector('i');
                    otherAnswer.classList.add('hidden');
                    otherIcon.classList.remove('rotate-180');
                }
            });
            
            // Toggle current item
            if (isOpen) {
                answer.classList.add('hidden');
                icon.classList.remove('rotate-180');
            } else {
                answer.classList.remove('hidden');
                icon.classList.add('rotate-180');
            }
        });
    });

    // Search functionality
    const searchInput = document.getElementById('faqSearch');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
