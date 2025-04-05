<?php
require_once '.config.php';
require_once 'auth.php';

checkAuthentication();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../account/dashboard.php');
    exit;
}

// Get sender info
$stmt = $pdo->prepare("SELECT u.*, a.balance FROM users u JOIN accounts a ON u.id = a.user_id WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$sender = $stmt->fetch();

// Validate recipient
$recipient_account = trim($_POST['recipient']);
$stmt = $pdo->prepare("SELECT u.*, a.balance FROM users u JOIN accounts a ON u.id = a.user_id WHERE u.account_number = ?");
$stmt->execute([$recipient_account]);
$recipient = $stmt->fetch();

if (!$recipient) {
    $_SESSION['error'] = "Recipient account not found";
    header('Location: ../account/transfer.php');
    exit;
}

// Validate amount
$amount = floatval($_POST['amount']);
if ($amount <= 0) {
    $_SESSION['error'] = "Amount must be greater than zero";
    header('Location: ../account/transfer.php');
    exit;
}

if ($sender['balance'] < $amount) {
    $_SESSION['error'] = "Insufficient funds";
    header('Location: ../account/transfer.php');
    exit;
}

// Start transaction
try {
    $pdo->beginTransaction();
    
    // Deduct from sender
    $new_sender_balance = $sender['balance'] - $amount;
    $stmt = $pdo->prepare("UPDATE accounts SET balance = ? WHERE user_id = ?");
    $stmt->execute([$new_sender_balance, $_SESSION['user_id']]);
    
    // Add to recipient
    $new_recipient_balance = $recipient['balance'] + $amount;
    $stmt = $pdo->prepare("UPDATE accounts SET balance = ? WHERE user_id = ?");
    $stmt->execute([$new_recipient_balance, $recipient['id']]);
    
    // Record transaction
    $stmt = $pdo->prepare("INSERT INTO transactions 
        (from_account, to_account, amount, description, sender_name, recipient_name) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $sender['account_number'],
        $recipient['account_number'],
        $amount,
        $_POST['description'] ?? '',
        $sender['full_name'],
        $recipient['full_name']
    ]);
    
    $pdo->commit();
    
    $_SESSION['success'] = "Transfer of $".number_format($amount,2)." to ".htmlspecialchars($recipient['full_name'])." was successful!";
    header('Location: ../account/dashboard.php');
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Transfer failed: ".$e->getMessage();
    header('Location: ../account/transfer.php');
}
?>