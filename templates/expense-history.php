<?php
/**
 * Expense History Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = 'Expense History - 120 Stand Inventory';
include STAND120_PLUGIN_DIR . 'templates/partials/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">
        <iconify-icon icon="solar:history-linear"></iconify-icon>
        Expense History
    </h1>
    <a href="<?php echo home_url('/120-stand/expense-record/'); ?>" class="btn btn-primary">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon> Back
    </a>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-group">
        <label>From Date</label>
        <input type="date" id="dateFrom" class="form-control" value="<?php echo date('Y-m-01'); ?>">
    </div>
    <div class="filter-group">
        <label>To Date</label>
        <input type="date" id="dateTo" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="filter-group">
        <label>&nbsp;</label>
        <button id="filterBtn" class="btn btn-primary">
            <iconify-icon icon="solar:filter-linear"></iconify-icon> Filter
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:money-bag-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Total Expenses</span>
        <span id="totalExpenses" class="summary-card-value">₦0</span>
    </div>
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:list-1-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Total Items</span>
        <span id="totalItems" class="summary-card-value">0</span>
    </div>
</div>

<!-- History Table -->
<div class="glass-card">
    <div class="table-responsive">
        <table class="table" id="historyTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Staff</th>
                    <th>Description</th>
                    <th>Amount (₦)</th>
                    <th>Qty</th>
                    <th>Total (₦)</th>
                </tr>
            </thead>
            <tbody id="historyBody">
                <!-- Data loaded via JavaScript -->
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        <button class="pagination-btn" id="prevPage" disabled>
            <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon> Previous
        </button>
        <span class="pagination-info">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
        <button class="pagination-btn" id="nextPage">
            Next <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
        </button>
    </div>
</div>

<script>
    let currentPage = 1;
    const perPage = 20;
    
    $(document).ready(function() {
        loadExpenses();
        
        $('#filterBtn').on('click', function() {
            currentPage = 1;
            loadExpenses();
        });
        
        $('#prevPage').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadExpenses();
            }
        });
        
        $('#nextPage').on('click', function() {
            currentPage++;
            loadExpenses();
        });
    });
    
    function loadExpenses() {
        Stand120.ajax('get_expense_history', {
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            page: currentPage,
            per_page: perPage
        }).then(response => {
            if (response.success) {
                renderExpenses(response.data.records || []);
                updateSummary(response.data.records || []);
                updatePagination(response.data);
            }
        });
    }
    
    function renderExpenses(records) {
        const $tbody = $('#historyBody').empty();
        
        if (records.length === 0) {
            $tbody.append('<tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No expenses found</td></tr>');
            return;
        }
        
        records.forEach(expense => {
            const amount = parseFloat(expense.amount) || 0;
            const qty = parseInt(expense.quantity) || 1;
            const total = amount * qty;
            $tbody.append(`<tr>
                <td>${expense.expense_date}</td>
                <td>${expense.staff_name || '-'}</td>
                <td>${expense.description}</td>
                <td class="formatted-number">₦${Stand120.formatNumber(amount)}</td>
                <td>${qty}</td>
                <td class="formatted-number" style="font-weight: 600;">₦${Stand120.formatNumber(total)}</td>
            </tr>`);
        });
    }
    
    function updateSummary(records) {
        let totalAmount = 0;
        records.forEach(expense => {
            const amount = parseFloat(expense.amount) || 0;
            const qty = parseInt(expense.quantity) || 1;
            totalAmount += amount * qty;
        });
        $('#totalExpenses').text('₦' + Stand120.formatNumber(totalAmount));
        $('#totalItems').text(records.length);
    }
    
    function updatePagination(data) {
        $('#currentPage').text(data.page);
        $('#totalPages').text(data.total_pages);
        $('#prevPage').prop('disabled', data.page <= 1);
        $('#nextPage').prop('disabled', data.page >= data.total_pages);
    }
</script>

<?php include STAND120_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
