<?php
/**
 * Product Summary Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = 'Product Summary - 120 Stand Inventory';
$current_user = Stand120_Auth::get_current_user_data();
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

<h1 class="page-title">
    <i class="fas fa-chart-bar"></i>
    Product Summary
</h1>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <i class="fas fa-shopping-basket"></i>
        </div>
        <span class="summary-card-label">Total Products Sold</span>
        <span id="totalProductsSold" class="summary-card-value">0</span>
    </div>
    
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <i class="fas fa-naira-sign"></i>
        </div>
        <span class="summary-card-label">Total Revenue</span>
        <span id="totalRevenue" class="summary-card-value">₦0</span>
    </div>
    
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <i class="fas fa-users"></i>
        </div>
        <span class="summary-card-label">Active Staff Today</span>
        <span id="activeStaff" class="summary-card-value">0</span>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-group">
        <label>From Date</label>
        <input type="date" id="dateFrom" class="form-control" value="<?php echo $today; ?>">
    </div>
    <div class="filter-group">
        <label>To Date</label>
        <input type="date" id="dateTo" class="form-control" value="<?php echo $today; ?>">
    </div>
    <div class="filter-group">
        <label>&nbsp;</label>
        <button id="filterBtn" class="btn btn-primary">
            <i class="fas fa-filter"></i> Apply Filter
        </button>
    </div>
</div>

<!-- Sales Breakdown Table -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <i class="fas fa-table"></i> Sales Breakdown
    </h3>
    
    <div class="table-responsive">
        <table class="table" id="summaryTable">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Staff</th>
                    <th>Quantity</th>
                    <th>Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data loaded via JavaScript -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
        <button class="pagination-btn" id="prevPage" disabled>
            <i class="fas fa-chevron-left"></i> Previous
        </button>
        <span class="pagination-info">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
        <button class="pagination-btn" id="nextPage">
            Next <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

<script>
    $(document).ready(function() {
        if (typeof ProductSummary !== 'undefined') {
            ProductSummary.init();
        }
    });
</script>

<?php include STAND120_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
