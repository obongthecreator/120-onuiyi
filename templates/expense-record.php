<?php
/**
 * Expense Record Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = 'Expense Record - 120 Stand Inventory';
$current_user = Stand120_Auth::get_current_user_data();
$is_admin = Stand120_Auth::is_admin();
$today = date('Y-m-d');

include STAND120_PLUGIN_DIR . 'templates/partials/header.php';
?>

<!-- Staff Info Bar -->
<div class="staff-info-bar">
    <div class="staff-info">
        <div class="staff-avatar">
            <?php echo strtoupper(substr($current_user['display_name'], 0, 1)); ?>
        </div>
        <div class="staff-details">
            <h4><?php echo esc_html($current_user['display_name']); ?></h4>
            <span><?php echo ucfirst($current_user['role']); ?></span>
        </div>
    </div>
    <div class="datetime-display">
        <div class="date-display">
            <i class="fas fa-calendar-alt"></i>
            <span class="date-text"><?php echo date_i18n('l, F j, Y'); ?></span>
        </div>
        <div class="time-display">
            <i class="fas fa-clock"></i>
            <span class="digital-clock">--:--:--</span>
        </div>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">
        <i class="fas fa-receipt"></i>
        Expense Record
    </h1>
    <div style="display: flex; gap: 12px; align-items: center;">
        <input type="date" id="expenseDate" class="form-control" value="<?php echo $today; ?>" style="max-width: 200px;">
        <a href="<?php echo home_url('/120-stand/expense-history/'); ?>" class="history-btn">
            <i class="fas fa-history"></i> View History
        </a>
    </div>
</div>

<!-- Expense Form -->
<form id="expenseForm">
    <div class="glass-card">
        <h3 style="margin-bottom: 16px; color: var(--primary-color);">
            <i class="fas fa-list"></i> Expense Items
        </h3>
        
        <div class="table-responsive">
            <table class="table" id="expenseTable">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount (₦)</th>
                        <th>Quantity</th>
                        <th>Total (₦)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="expenseBody">
                    <!-- Rows added via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 16px;">
            <button type="button" id="addExpenseRow" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Expense
            </button>
        </div>
    </div>
    
    <!-- Grand Total -->
    <div class="grand-total-section">
        <span class="grand-total-label">
            <i class="fas fa-calculator"></i> Grand Total
        </span>
        <span id="grandTotal" class="grand-total-value">₦0</span>
    </div>
    
    <!-- Submit Button -->
    <div style="margin-top: 24px; text-align: center;">
        <button type="button" id="submitExpenses" class="btn btn-primary btn-lg">
            <i class="fas fa-check-circle"></i> Submit Expenses
        </button>
    </div>
</form>

<script>
    jQuery(document).ready(function($) {
        let rowCounter = 0;
        
        function addRow() {
            rowCounter++;
            const row = `
                <tr data-row="${rowCounter}">
                    <td>
                        <input type="text" class="table-input expense-desc" placeholder="Enter description" required>
                    </td>
                    <td>
                        <input type="number" class="table-input expense-amount" placeholder="0" min="0" step="any" value="">
                    </td>
                    <td>
                        <input type="number" class="table-input expense-qty" placeholder="1" min="1" value="1" style="max-width: 80px;">
                    </td>
                    <td class="row-total formatted-number">₦0</td>
                    <td>
                        <button type="button" class="btn remove-row-btn" style="background: var(--danger-color); color: #fff; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#expenseBody').append(row);
            recalculate();
        }
        
        function recalculate() {
            let grandTotal = 0;
            $('#expenseBody tr').each(function() {
                const amount = parseFloat($(this).find('.expense-amount').val()) || 0;
                const qty = parseInt($(this).find('.expense-qty').val()) || 1;
                const total = amount * qty;
                grandTotal += total;
                $(this).find('.row-total').text('₦' + Stand120.formatNumber(total));
            });
            $('#grandTotal').text('₦' + Stand120.formatNumber(grandTotal));
        }
        
        // Add first row by default
        addRow();
        
        $('#addExpenseRow').on('click', function() {
            addRow();
        });
        
        // Remove row
        $('#expenseBody').on('click', '.remove-row-btn', function() {
            if ($('#expenseBody tr').length > 1) {
                $(this).closest('tr').remove();
                recalculate();
            } else {
                Stand120.showAlert('error', 'At least one expense row is required.');
            }
        });
        
        // Recalculate on input change
        $('#expenseBody').on('input', '.expense-amount, .expense-qty', function() {
            recalculate();
        });
        
        // Submit expenses
        $('#submitExpenses').on('click', function() {
            const items = [];
            let valid = true;
            
            $('#expenseBody tr').each(function() {
                const desc = $(this).find('.expense-desc').val().trim();
                const amount = parseFloat($(this).find('.expense-amount').val()) || 0;
                const qty = parseInt($(this).find('.expense-qty').val()) || 1;
                
                if (desc === '' && amount === 0) {
                    return; // skip empty rows
                }
                
                if (desc === '') {
                    valid = false;
                    Stand120.showAlert('error', 'Please enter a description for all expense items.');
                    return false;
                }
                
                if (amount <= 0) {
                    valid = false;
                    Stand120.showAlert('error', 'Please enter a valid amount for "' + desc + '".');
                    return false;
                }
                
                items.push({
                    description: desc,
                    amount: amount,
                    quantity: qty,
                    total: amount * qty
                });
            });
            
            if (!valid) return;
            
            if (items.length === 0) {
                Stand120.showAlert('error', 'Please add at least one expense item.');
                return;
            }
            
            const date = $('#expenseDate').val();
            if (!date) {
                Stand120.showAlert('error', 'Please select a date.');
                return;
            }
            
            Stand120.ajax('submit_expenses', {
                expenses: JSON.stringify(items),
                date: date
            }).then(response => {
                if (response.success) {
                    Stand120.showAlert('success', response.data.message || 'Expenses submitted successfully!');
                    // Reset form
                    $('#expenseBody').empty();
                    rowCounter = 0;
                    addRow();
                    $('#grandTotal').text('₦0');
                } else {
                    Stand120.showAlert('error', response.data || 'Failed to submit expenses.');
                }
            }).catch(() => {
                Stand120.showAlert('error', 'An error occurred. Please try again.');
            });
        });
    });
</script>

<?php include STAND120_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
