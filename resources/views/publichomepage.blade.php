@extends('layouts.publicpageslayout')

@section('page_title')
    Public Homepage
@endsection

@section('page_content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Revolutionize Your Fueling Experience</h1>
                <p class="hero-subtitle">Fast, smart, and seamless self-refueling powered by vehicle recognition.</p>
                <button class="btn btn-cta">Get Started</button>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <div class="steps-grid">
                <div class="step-card fade-in" data-step="1">
                    <div class="step-icon">📝</div>
                    <h3>Register on Platform</h3>
                    <p>Create your account and add your vehicle details to our secure platform.</p>
                </div>
                <div class="step-card fade-in" data-step="2">
                    <div class="step-icon">🚗</div>
                    <h3>Drive to Station</h3>
                    <p>Visit any of our partnered fuel stations for a seamless experience.</p>
                </div>
                <div class="step-card fade-in" data-step="3">
                    <div class="step-icon">📷</div>
                    <h3>License Plate Recognition</h3>
                    <p>Our smart cameras automatically read your vehicle's number plate.</p>
                </div>
                <div class="step-card fade-in" data-step="4">
                    <div class="step-icon">🚪</div>
                    <h3>Automatic Entry</h3>
                    <p>Entry gate opens automatically once your vehicle is recognized.</p>
                </div>
                <div class="step-card fade-in" data-step="5">
                    <div class="step-icon">⛽</div>
                    <h3>Fuel Your Vehicle</h3>
                    <p>Simply fuel your vehicle inside the station - no cards needed.</p>
                </div>
                <div class="step-card fade-in" data-step="6">
                    <div class="step-icon">💳</div>
                    <h3>Auto Payment</h3>
                    <p>Gate opens and your account is automatically charged for fuel.</p>
                </div>
                <div class="step-card fade-in" data-step="7">
                    <div class="step-icon">📧</div>
                    <h3>Email Receipt</h3>
                    <p>Instant email receipt sent to your registered email address.</p>
                </div>
                <div class="step-card fade-in" data-step="8">
                    <div class="step-icon">🔄</div>
                    <h3>Easy Recharge</h3>
                    <p>Recharge your account by uploading payment slips through our portal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Fill and Go Section -->
    <section class="why-fill-and-go">
        <div class="container">
            <h2 class="section-title">Why Fill and Go?</h2>
            <div class="benefits-grid">
                <div class="benefit-card slide-in-left">
                    <div class="benefit-icon">⚡</div>
                    <h3>Save Time</h3>
                    <p>Skip the queues and payment hassles. Our automated system gets you back on the road faster.</p>
                </div>
                <div class="benefit-card slide-in-right">
                    <div class="benefit-icon">📊</div>
                    <h3>Track Consumption</h3>
                    <p>Monitor your fuel usage patterns and expenses with detailed analytics and reports.</p>
                </div>
                <div class="benefit-card slide-in-left">
                    <div class="benefit-icon">✨</div>
                    <h3>Hassle-Free Transactions</h3>
                    <p>No more fumbling for cards or cash. Everything happens automatically and securely.</p>
                </div>
                <div class="benefit-card slide-in-right">
                    <div class="benefit-icon">💼</div>
                    <h3>Convenient Management</h3>
                    <p>Manage your account, view history, and top up your balance from anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Top-Up Process Section -->
    <section class="top-up-process">
        <div class="container">
            <h2 class="section-title">Simple Top-Up Process</h2>
            <div class="process-steps">
                <div class="process-card fade-in">
                    <div class="process-icon">📤</div>
                    <h3>Upload Payment Slip</h3>
                    <p>Simply upload your payment slip through our secure web portal.</p>
                </div>
                <div class="process-arrow">→</div>
                <div class="process-card fade-in">
                    <div class="process-icon">✅</div>
                    <h3>Admin Confirmation</h3>
                    <p>Our admins quickly verify and confirm your payment details.</p>
                </div>
                <div class="process-arrow">→</div>
                <div class="process-card fade-in">
                    <div class="process-icon">💰</div>
                    <h3>Balance Recharge</h3>
                    <p>Your account balance is recharged and ready for your next fuel-up.</p>
                </div>
            </div>
            <div class="balance-tracking fade-in">
                <h3>Easy Balance Tracking</h3>
                <p>Track your balance and transaction history from your personalized dashboard.</p>
            </div>
        </div>
    </section>
@endsection
