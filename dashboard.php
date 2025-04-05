<?php
require_once 'onfig.php';
require_once 'auth.php';

// Check authentication
checkAuthentication();

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get account balance
$stmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$account = $stmt->fetch();

// Get recent transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE from_account = ? OR to_account = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user['account_number'], $user['account_number']]);
$transactions = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="user-profile">
            <img src="../assets/images/avatar.png" alt="User Avatar">
            <h3><?= htmlspecialchars($user['full_name']) ?></h3>
            <p>Account: <?= htmlspecialchars($user['account_number']) ?></p>
        </div>
        <nav>
            <ul>
                <li class="active"><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="transfer.php"><i class="fas fa-exchange-alt"></i> Transfer Money</a></li>
                <li><a href="history.php"><i class="fas fa-history"></i> Transaction History</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="balance-card">
            <h2>Available Balance</h2>
            <div class="balance-amount">$<?= number_format($account['balance'], 2) ?></div>
            <div class="balance-actions">
                <button class="btn btn-primary" onclick="showTransferModal()">Transfer Money</button>
                <button class="btn btn-outline">Deposit</button>
            </div>
        </div>
        
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-grid">
                <div class="action-item">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transfer</span>
                </div>
                <div class="action-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Pay Bills</span>
                </div>
                <div class="action-item">
                    <i class="fas fa-qrcode"></i>
                    <span>Scan to Pay</span>
                </div>
                <div class="action-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Cards</span>
                </div>
            </div>
        </div>
        
        <div class="recent-transactions">
            <h3>Recent Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $tx): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                        <td>
                            <?= $tx['from_account'] == $user['account_number'] ? 
                                'To: ' . htmlspecialchars($tx['recipient_name']) : 
                                'From: ' . htmlspecialchars($tx['sender_name']) ?>
                        </td>
                        <td class="<?= $tx['from_account'] == $user['account_number'] ? 'text-danger' : 'text-success' ?>">
                            <?= $tx['from_account'] == $user['account_number'] ? '-' : '+' ?>
                            $<?= number_format($tx['amount'], 2) ?>
                        </td>
                        <td><span class="badge badge-success">Completed</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="history.php" class="view-all">View All Transactions</a>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="hideTransferModal()">&times;</span>
        <h2>Transfer Money</h2>
        <form action="../transactions/process_transfer.php" method="POST">
            <div class="form-group">
                <label for="recipient">Recipient Account Number</label>
                <input type="text" id="recipient" name="recipient" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" min="1" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description">
            </div>
            <button type="submit" class="btn btn-primary">Transfer Now</button>
        </form>
    </div>
</div>

<script src="script.js"></script>
<?php require_once 'footer.php'; ?>