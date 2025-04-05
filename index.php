<?php
require_once 'config.php';
require_once 'header.php';
?>

<div class="hero-section">
    <div class="container">
        <h1>Secure Online Banking</h1>
        <p>Transfer money, pay bills, and manage your finances with ease</p>
        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-outline">Register</a>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <div class="feature-card">
            <i class="fas fa-exchange-alt"></i>
            <h3>Instant Transfers</h3>
            <p>Send money to anyone, anywhere in seconds</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-shield-alt"></i>
            <h3>Bank-Level Security</h3>
            <p>256-bit encryption protects your transactions</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-chart-line"></i>
            <h3>Financial Insights</h3>
            <p>Track your spending with powerful analytics</p>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>