// Show transfer modal
function showTransferModal() {
    document.getElementById('transferModal').style.display = 'flex';
}

// Hide transfer modal
function hideTransferModal() {
    document.getElementById('transferModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('transferModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Form validation for transfer
document.querySelector('#transferForm')?.addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('amount').value);
    const balance = parseFloat(document.getElementById('balance').textContent.replace('$', ''));
    
    if (amount > balance) {
        e.preventDefault();
        alert('Insufficient funds for this transfer');
    }
});

// Animate balance on dashboard
function animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = "$" + Math.floor(progress * (end - start) + start).toLocaleString();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Animate balance when page loads
document.addEventListener('DOMContentLoaded', function() {
    const balanceElement = document.querySelector('.balance-amount');
    if (balanceElement) {
        const balance = parseFloat(balanceElement.textContent.replace('$', '').replace(',', ''));
        balanceElement.textContent = "$0";
        animateValue('balance', 0, balance, 1000);
    }
});