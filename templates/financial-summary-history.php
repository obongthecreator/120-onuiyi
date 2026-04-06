<?php
/**
 * Financial Summary History Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = 'Financial Summary History - 120 Stand Inventory';
include STAND120_PLUGIN_DIR . 'templates/partials/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">
        <i class="fas fa-history"></i>
        Financial Summary History
    </h1>
    <a href="<?php echo home_url('/120-stand/financial-summary/'); ?>" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

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
        <div style="display: flex; gap: 8px;">
            <button id="filterBtn" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button id="recalcBtn" class="btn btn-primary" style="background: var(--warning-color); border-color: var(--warning-color);">
                <i class="fas fa-sync-alt"></i> Recalculate
            </button>
        </div>
    </div>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table" id="historyTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Sales</th>
                    <th>Cash</th>
                    <th>Transfer</th>
                    <th>Extras</th>
                    <th>Expense</th>
                    <th>Mkt Card Exp Left</th>
                    <th>Old Cash</th>
                    <th>Cash Left</th>
                </tr>
            </thead>
            <tbody id="historyBody"></tbody>
        </table>
    </div>
    
    <div class="pagination">
        <button class="pagination-btn" id="prevPage" disabled><i class="fas fa-chevron-left"></i> Previous</button>
        <span class="pagination-info">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
        <button class="pagination-btn" id="nextPage">Next <i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<script>
    let currentPage = 1;
    
    $(document).ready(function() {
        loadHistory();
        
        $('#filterBtn').on('click', () => { currentPage = 1; loadHistory(); });
        $('#prevPage').on('click', () => { if (currentPage > 1) { currentPage--; loadHistory(); }});
        $('#nextPage').on('click', () => { currentPage++; loadHistory(); });
        
        // Manual recalculate button
        $('#recalcBtn').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Recalculating...');
            
            Stand120.ajax('recalculate_financial_history', {
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val()
            }).then(response => {
                if (response.success) {
                    Stand120.showAlert('success', response.data.message);
                    loadHistory();
                } else {
                    Stand120.showAlert('danger', response.data?.message || 'Recalculation failed');
                }
            }).catch(() => {
                Stand120.showAlert('danger', 'Recalculation failed');
            }).finally(() => {
                $btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Recalculate');
            });
        });
    });
    
    function loadHistory() {
        Stand120.ajax('get_financial_summary_history', {
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            page: currentPage,
            per_page: 20
        }).then(response => {
            if (response.success) {
                const $tbody = $('#historyBody').empty();
                if (response.data.records.length === 0) {
                    $tbody.append('<tr><td colspan="9" style="text-align:center;color:var(--text-muted)">No records found</td></tr>');
                } else {
                    response.data.records.forEach(r => {
                        $tbody.append(`<tr>
                            <td>${r.summary_date}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.total_sales)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.cash_sales)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.transfer_sales)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.extras_amount)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.expenses_amount)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.market_card_expense)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(r.old_cash)}</td>
                            <td class="formatted-number" style="font-weight:600;color:var(--primary-color)">₦${Stand120.formatNumber(r.cash_left)}</td>
                        </tr>`);
                    });
                }
                $('#currentPage').text(response.data.page);
                $('#totalPages').text(response.data.total_pages);
                $('#prevPage').prop('disabled', response.data.page <= 1);
                $('#nextPage').prop('disabled', response.data.page >= response.data.total_pages);
            }
        });
    }
</script>

<?php include STAND120_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
