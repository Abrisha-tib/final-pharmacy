<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Analog Pharmacy System') }} - Login</title>
    
    <!-- Tailwind CSS - Modern utility-first CSS framework -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome - Optimized for performance -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous">
    <!-- Google Fonts - Modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Modern React-inspired Design System */
        :root {
            --primary-green: #059669;
            --primary-green-light: #10b981;
            --primary-orange: #f59e0b;
            --primary-orange-light: #fbbf24;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --red-500: #ef4444;
            --red-100: #fee2e2;
            --green-500: #10b981;
            --green-100: #d1fae5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes backgroundShift {
            0%, 100% { transform: translateX(0) translateY(0) rotate(0deg); }
            33% { transform: translateX(-20px) translateY(-10px) rotate(1deg); }
            66% { transform: translateX(20px) translateY(10px) rotate(-1deg); }
        }

        /* Floating particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Modern glassmorphism card - more compact */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 
                0 20px 40px -12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            max-width: 380px;
            width: 100%;
            overflow: hidden;
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: cardEntrance 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 35px 60px -12px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        /* Modern header with gradient - more compact */
        .login-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem auto;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand-logo:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .login-header h3 {
            font-weight: 700;
            font-size: 1.875rem;
            margin-bottom: 0.5rem;
            color: white;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-header p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            position: relative;
            z-index: 1;
            font-weight: 400;
        }

        /* Modern form styling - more compact */
        .form-container {
            padding: 1.5rem 1.5rem 2rem 1.5rem;
            background: white;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--gray-50);
            color: var(--gray-900);
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            background: white;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
            transform: translateY(-1px);
        }

        .form-input:hover {
            border-color: var(--gray-300);
            background: white;
        }

        .form-input.error {
            border-color: var(--red-500);
            background: var(--red-100);
        }

        .form-input.success {
            border-color: var(--green-500);
            background: var(--green-100);
        }

        /* Password toggle */
        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--primary-green);
            background: rgba(5, 150, 105, 0.1);
        }

        .password-toggle:focus {
            outline: none;
            color: var(--primary-green);
            background: rgba(5, 150, 105, 0.1);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
        }

        /* Modern checkbox - more compact */
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .checkbox-input {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid var(--gray-300);
            border-radius: 6px;
            margin-right: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .checkbox-input:checked {
            background: var(--primary-green);
            border-color: var(--primary-green);
        }

        .checkbox-input:focus {
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            cursor: pointer;
            font-weight: 500;
        }

        /* Modern button - more compact */
        .btn-primary {
            width: 100%;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-light) 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Modern link */
        .forgot-link {
            color: var(--primary-green);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 1rem;
        }

        .forgot-link:hover {
            color: var(--primary-green-light);
            text-decoration: underline;
            transform: translateY(-1px);
        }

        /* Enhanced loading states */
        .loading {
            display: none;
        }

        .btn-primary.loading .btn-text {
            display: none;
        }

        .btn-primary.loading .loading {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Loading spinner animations */
        .btn-primary.loading .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Loading state transitions */
        .btn-primary.loading {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-primary.loading:hover {
            transform: none;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        /* Loading text animation */
        #loadingText {
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 24px;
        }

        .loading-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .loading-content {
            text-align: center;
            padding: 2rem;
        }

        .loading-spinner {
            font-size: 2rem;
            color: var(--primary-orange);
            margin-bottom: 1rem;
            animation: spin 1s linear infinite;
        }

        .loading-message {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 1.5rem;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .loading-progress {
            width: 200px;
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
            margin: 0 auto;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-light) 100%);
            border-radius: 2px;
            width: 0%;
            transition: width 0.8s ease-in-out;
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: 200px 0; }
        }

        /* Developer info section directly below the card */
        .developer-info-section {
            margin-top: 1.5rem;
            text-align: center;
            max-width: 380px;
            width: 100%;
        }

        .developer-info {
            margin-bottom: 1rem;
        }

        .developer-text {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin: 0 0 0.25rem 0;
            font-weight: 400;
        }

        .company-name {
            font-size: 0.875rem;
            color: var(--primary-green);
            margin: 0 0 0.25rem 0;
            font-weight: 600;
        }

        .location {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin: 0;
            font-weight: 400;
        }

        .copyright-info {
            margin-top: 0.75rem;
        }

        .copyright {
            font-size: 0.7rem;
            color: var(--gray-400);
            margin: 0;
            font-weight: 400;
        }

        /* Developer info responsive design */
        @media (max-width: 480px) {
            .developer-info-section {
                margin-top: 1.5rem;
                max-width: 350px;
            }
            
            .developer-text,
            .location,
            .copyright {
                font-size: 0.7rem;
            }
            
            .company-name {
                font-size: 0.8rem;
            }
        }

        /* Error messages */
        .error-message {
            color: var(--red-500);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Success messages */
        .success-message {
            background: linear-gradient(135deg, var(--green-100) 0%, #d1fae5 100%);
            border: 1px solid var(--green-500);
            color: var(--primary-green);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        /* Responsive design - more compact */
        @media (max-width: 480px) {
            .login-card {
                margin: 0.5rem;
                border-radius: 16px;
                max-width: 350px;
            }
            
            .form-container {
                padding: 1.25rem 1.25rem 1.5rem 1.25rem;
            }
            
            .login-header {
                padding: 1.5rem 1.25rem 1.25rem 1.25rem;
            }
            
            .brand-logo {
                width: 40px;
                height: 40px;
                margin-bottom: 0.75rem;
            }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* High contrast mode */
        @media (prefers-contrast: high) {
            .form-input {
                border-width: 3px;
            }
            
            .btn-primary {
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>
    <!-- Floating particles for modern effect -->
    <div class="particles" id="particles"></div>
    
    <!-- Main login container -->
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="login-card">
            <!-- Modern header with brand logo -->
            <div class="login-header">
                <img src="{{ asset('analo.png') }}" alt="Analog Pharmacy" class="brand-logo">
                <h3>Analog Pharmacy System</h3>
                <p>Sign in to your account</p>
            </div>
            
            <!-- Modern form container -->
            <div class="form-container">
                <!-- Loading overlay -->
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="loading-content">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        <div class="loading-message" id="overlayLoadingText">Signing you in...</div>
                        <div class="loading-progress">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Success/Status messages -->
                @if (session('status'))
                    <div class="success-message" role="alert" aria-live="polite">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('status') }}
                    </div>
                @endif
                
                <!-- Modern login form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf
                    
                    <!-- Email field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" 
                               type="email" 
                               class="form-input @error('email') error @enderror" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus
                               placeholder="Enter your email address">
                        
                        @error('email')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password field with toggle -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-container">
                            <input id="password" 
                                   type="password" 
                                   class="form-input @error('password') error @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Enter your password">
                            <button type="button" 
                                    class="password-toggle" 
                                    id="passwordToggle"
                                    aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember me checkbox -->
                    <div class="checkbox-container">
                        <input class="checkbox-input" 
                               type="checkbox" 
                               name="remember" 
                               id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="checkbox-label" for="remember">
                            Remember me for 30 days
                        </label>
                        </div>

                    <!-- Login button -->
                    <button type="submit" 
                            class="btn-primary" 
                            id="loginBtn">
                        <span class="btn-text">Sign In</span>
                        <span class="loading" id="loadingState">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            <span id="loadingText">Signing in...</span>
                        </span>
                                </button>

                    <!-- Forgot password link -->
                                @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Forgot your password?
                                    </a>
                        </div>
                    @endif
                    </form>
                </div>
        </div>
        
        <!-- Developer info directly below the card -->
        <div class="developer-info-section">
            <div class="developer-info">
                <p class="developer-text">System developed by</p>
                <p class="company-name">Analog Software Solutions</p>
                <p class="location">Arba Minch, Ethiopia</p>
            </div>
            <div class="copyright-info">
                <p class="copyright">&copy; {{ date('Y') }} Analog Software Solutions. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        /**
         * Modern React-inspired Login Form Handler
         * Optimized for performance, accessibility, and modern UX
         */
        (function() {
            'use strict';
            
            // DOM elements
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');
            const rememberCheckbox = document.getElementById('remember');
            
            // Create floating particles for modern effect
            function createParticles() {
                const particlesContainer = document.getElementById('particles');
                const particleCount = 20;
                
                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.width = Math.random() * 4 + 2 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.animationDelay = Math.random() * 15 + 's';
                    particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                    particlesContainer.appendChild(particle);
                }
            }
            
            // Enhanced loading state with detailed progress messages and overlay
            function showLoadingState() {
                // Show button loading state
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                loginBtn.setAttribute('aria-busy', 'true');
                
                const btnText = loginBtn.querySelector('.btn-text');
                const loadingText = loginBtn.querySelector('#loadingText');
                const loadingState = loginBtn.querySelector('#loadingState');
                
                btnText.style.display = 'none';
                loadingState.style.display = 'inline-flex';
                
                // Show loading overlay
                const loadingOverlay = document.getElementById('loadingOverlay');
                const overlayLoadingText = document.getElementById('overlayLoadingText');
                const progressBar = document.getElementById('progressBar');
                
                loadingOverlay.classList.add('show');
                
                // Detailed loading messages with progress bar
                const loadingMessages = [
                    { text: 'Validating credentials...', progress: 20 },
                    { text: 'Authenticating user...', progress: 40 },
                    { text: 'Checking permissions...', progress: 60 },
                    { text: 'Preparing dashboard...', progress: 80 },
                    { text: 'Almost ready...', progress: 95 }
                ];
                
                let messageIndex = 0;
                const messageInterval = setInterval(() => {
                    if (messageIndex < loadingMessages.length) {
                        const currentMessage = loadingMessages[messageIndex];
                        loadingText.textContent = currentMessage.text;
                        overlayLoadingText.textContent = currentMessage.text;
                        progressBar.style.width = currentMessage.progress + '%';
                        messageIndex++;
                    } else {
                        clearInterval(messageInterval);
                        // Final progress
                        progressBar.style.width = '100%';
                    }
                }, 1000);
                
                // Store interval for cleanup
                loginBtn.loadingInterval = messageInterval;
            }
            
            // Modern form validation with visual feedback
            function validateField(field) {
                const isValid = field.checkValidity();
                field.classList.toggle('success', isValid && field.value.length > 0);
                field.classList.toggle('error', !isValid && field.value.length > 0);
                return isValid;
            }
            
            // Modern input animations with React-inspired transitions
            function addInputAnimations() {
                const inputs = [emailField, passwordField];
                
                inputs.forEach(input => {
                    // Focus animations with smooth transitions
                    input.addEventListener('focus', function() {
                        this.parentElement.style.transform = 'translateY(-2px)';
                        this.parentElement.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    });
                    
                    // Blur animations
                    input.addEventListener('blur', function() {
                        this.parentElement.style.transform = 'translateY(0)';
                        validateField(this);
                    });
                    
                    // Real-time validation with smooth feedback
                    input.addEventListener('input', function() {
                        if (this.value.length > 0) {
                            validateField(this);
                        }
                    });
                });
            }
            
            // Modern form submission with enhanced UX
            function handleFormSubmit(e) {
                e.preventDefault();
                
                // Validate all fields
                const isEmailValid = validateField(emailField);
                const isPasswordValid = validateField(passwordField);
                
                if (!isEmailValid || !isPasswordValid) {
                    // Focus first invalid field with smooth animation
                    const firstInvalid = loginForm.querySelector('.error');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }
                
                // Show loading state with smooth transition
                showLoadingState();
                
                // Submit form
                loginForm.submit();
            }
            
            // Modern password toggle with smooth animations
            function addPasswordToggle() {
                passwordToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        passwordToggleIcon.classList.remove('fa-eye');
                        passwordToggleIcon.classList.add('fa-eye-slash');
                        passwordToggle.classList.add('active');
                        passwordToggle.setAttribute('aria-label', 'Hide password');
                    } else {
                        passwordField.type = 'password';
                        passwordToggleIcon.classList.remove('fa-eye-slash');
                        passwordToggleIcon.classList.add('fa-eye');
                        passwordToggle.classList.remove('active');
                        passwordToggle.setAttribute('aria-label', 'Show password');
                    }
                });
            }
            
            // Modern keyboard navigation with enhanced shortcuts
            function addKeyboardNavigation() {
                document.addEventListener('keydown', function(e) {
                    // Enter key on remember checkbox
                    if (e.key === 'Enter' && document.activeElement === rememberCheckbox) {
                        rememberCheckbox.checked = !rememberCheckbox.checked;
                        e.preventDefault();
                    }
                    
                    // Alt+P to toggle password visibility
                    if (e.altKey && e.key === 'p') {
                        e.preventDefault();
                        passwordToggle.click();
                    }
                    
                    // Escape to clear form
                    if (e.key === 'Escape') {
                        emailField.value = '';
                        passwordField.value = '';
                        emailField.focus();
                    }
                });
            }
            
            // Modern auto-focus with smooth entrance
            function autoFocusEmail() {
                setTimeout(() => {
                    emailField.focus();
                    emailField.select();
                }, 300);
            }
            
            // Modern form reset with smooth transitions
            function resetFormState() {
                // Clear any existing loading interval
                if (loginBtn.loadingInterval) {
                    clearInterval(loginBtn.loadingInterval);
                    loginBtn.loadingInterval = null;
                }
                
                // Hide loading overlay
                const loadingOverlay = document.getElementById('loadingOverlay');
                const progressBar = document.getElementById('progressBar');
                
                loadingOverlay.classList.remove('show');
                progressBar.style.width = '0%';
                
                // Reset button state
                loginBtn.classList.remove('loading');
                loginBtn.disabled = false;
                loginBtn.setAttribute('aria-busy', 'false');
                
                const btnText = loginBtn.querySelector('.btn-text');
                const loadingState = loginBtn.querySelector('#loadingState');
                const loadingText = loginBtn.querySelector('#loadingText');
                
                btnText.style.display = 'inline';
                loadingState.style.display = 'none';
                loadingText.textContent = 'Signing in...'; // Reset to default message
            }
            
            // Initialize modern login form
            function initModernLogin() {
                // Create floating particles
                createParticles();
                
                // Add event listeners
                loginForm.addEventListener('submit', handleFormSubmit);
                
                // Add modern animations
                addInputAnimations();
                
                // Add password toggle
                addPasswordToggle();
                
                // Add keyboard navigation
                addKeyboardNavigation();
                
                // Auto-focus with delay
                autoFocusEmail();
                
                // Reset form state
                resetFormState();
                
                // Add form validation on page load
                if (emailField.value) {
                    validateField(emailField);
                }
                if (passwordField.value) {
                    validateField(passwordField);
                }
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initModernLogin);
            } else {
                initModernLogin();
            }
            
            // Modern error handling
            window.addEventListener('error', function(e) {
                console.error('Login form error:', e.error);
                resetFormState();
            });
            
        })();
    </script>
</body>
</html>
