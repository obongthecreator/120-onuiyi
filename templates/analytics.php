<?php
/**
 * Analytics Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!Stand120_Auth::is_admin()) {
    wp_redirect(home_url('/120-stand/'));
    exit;
}

$page_title = 'Analytics - 120 Stand Inventory';
$current_user = Stand120_Auth::get_current_user_data();

include STAND120_PLUGIN_DIR . 'templates/partials/header.php';
?>

<h1 class="page-title">
    <iconify-icon icon="solar:graph-up-linear"></iconify-icon>
    Analytics Dashboard
</h1>

<!-- Period Filter -->
<div class="filter-section">
    <div class="filter-group">
        <label>Period</label>
        <select id="periodFilter" class="form-control">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Date</label>
        <input type="date" id="periodDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="filter-group">
        <label>&nbsp;</label>
        <button id="loadAnalytics" class="btn btn-primary">
            <iconify-icon icon="solar:refresh-linear"></iconify-icon> Load Analytics
        </button>
    </div>
</div>

<!-- Overview Cards -->
<div class="summary-cards">
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:graph-up-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Revenue</span>
        <span id="revenueAnalytics" class="summary-card-value">₦0</span>
    </div>

    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:pie-chart-2-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Profit</span>
        <span id="profitAnalytics" class="summary-card-value">₦0</span>
    </div>

    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:minus-circle-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Expenses</span>
        <span id="expensesAnalytics" class="summary-card-value">₦0</span>
    </div>

    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:arrow-down-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Loss</span>
        <span id="lossAnalytics" class="summary-card-value">₦0</span>
    </div>
</div>

<div class="summary-cards">
    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:money-bag-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Total Sales</span>
        <span id="totalSalesAnalytics" class="summary-card-value">₦0</span>
    </div>

    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:cart-large-2-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Total Orders</span>
        <span id="totalOrdersAnalytics" class="summary-card-value">0</span>
    </div>

    <div class="summary-card glass-card">
        <div class="summary-card-icon">
            <iconify-icon icon="solar:calculator-linear"></iconify-icon>
        </div>
        <span class="summary-card-label">Avg Order Value</span>
        <span id="avgOrderAnalytics" class="summary-card-value">₦0</span>
    </div>
</div>

<!-- Target Setting Section -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:target-linear"></iconify-icon> Sales Target Recommendation
    </h3>
    <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px;">
        <div class="filter-group">
            <label>Target Amount (₦)</label>
            <input type="number" id="targetAmount" class="form-control" placeholder="Enter target amount" min="1" step="100">
        </div>
        <div class="filter-group">
            <label>&nbsp;</label>
            <button id="calculateTarget" class="btn btn-primary">
                <iconify-icon icon="solar:calculator-linear"></iconify-icon> Calculate
            </button>
        </div>
    </div>
    <div id="targetResult" style="display:none;">
        <div id="targetSummary" style="color: var(--text-muted); margin-bottom: 12px;"></div>
        <div class="table-responsive">
            <table class="table" id="targetRecommendationTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Suggested Qty</th>
                        <th>Avg Daily Sales</th>
                        <th>Projected Revenue</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Revenue Attribution -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:graph-up-linear"></iconify-icon> Product Revenue Attribution
    </h3>
    <div class="table-responsive">
        <table class="table" id="productRevenueTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty Sold</th>
                    <th>Unit Price</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Detailed Expenses Breakdown -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:minus-circle-linear"></iconify-icon> Detailed Expenses Breakdown
    </h3>
    <div class="table-responsive">
        <table class="table" id="detailedExpensesTable">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Staff Performance -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Staff Performance
    </h3>

    <div class="table-responsive">
        <table class="table" id="staffPerformanceTable">
            <thead>
                <tr>
                    <th>Staff Name</th>
                    <th>Orders</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Top Products -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:cup-star-linear"></iconify-icon> Top Selling Products
    </h3>

    <div class="table-responsive">
        <table class="table" id="topProductsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Order Preparation Analytics -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:chef-hat-linear"></iconify-icon> Order Preparation Analytics
    </h3>
    <div id="prepSummary" style="color: var(--text-muted); margin-bottom: 12px;"></div>
    <div class="table-responsive">
        <table class="table" id="prepAnalyticsTable">
            <thead>
                <tr>
                    <th>Fruit</th>
                    <th>Total Added</th>
                    <th>Total Sold</th>
                    <th>Closing</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Stock Inventory Analytics -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:box-linear"></iconify-icon> Stock Inventory Analytics
    </h3>
    <div id="stockSummary" style="color: var(--text-muted); margin-bottom: 12px;"></div>
    <div class="table-responsive">
        <table class="table" id="stockAnalyticsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Added Packs</th>
                    <th>Used Packs</th>
                    <th>Closing Packs</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Chopping Inventory Analytics -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:scissors-linear"></iconify-icon> Chopping Inventory Analytics
    </h3>
    <div id="chopSummary" style="color: var(--text-muted); margin-bottom: 12px;"></div>
    <div class="table-responsive">
        <table class="table" id="chopAnalyticsTable">
            <thead>
                <tr>
                    <th>Fruit</th>
                    <th>Imported Whole</th>
                    <th>Prepared Whole</th>
                    <th>Packs Gotten</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Import Records Analytics -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:delivery-linear"></iconify-icon> Import Records Analytics
    </h3>
    <div id="importSummary" style="color: var(--text-muted); margin-bottom: 12px;"></div>
    <div class="table-responsive">
        <table class="table" id="importAnalyticsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity Imported</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Financial Summary Analytics -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:wallet-linear"></iconify-icon> Financial Summary Analytics
    </h3>
    <div class="table-responsive">
        <table class="table" id="financialAnalyticsTable">
            <thead>
                <tr>
                    <th>Total Sales</th>
                    <th>Cash Sales</th>
                    <th>Transfer Sales</th>
                    <th>Delivery Fees</th>
                    <th>Extras</th>
                    <th>Expenses</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Daily Sales Chart (Simple visualization) -->
<div class="glass-card">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <iconify-icon icon="solar:chart-2-linear"></iconify-icon> Daily Sales Trend
    </h3>

    <div id="dailySalesChart" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-end; min-height: 200px;">
        <!-- Simple bar chart rendered via JS -->
    </div>
</div>

<script>
    $(document).ready(function() {
        loadAnalytics();

        $('#loadAnalytics').on('click', loadAnalytics);
        $('#periodFilter, #periodDate').on('change', loadAnalytics);
        $('#calculateTarget').on('click', calculateTarget);
    });

    function calculateTarget() {
        const targetAmount = parseFloat($('#targetAmount').val());
        if (!targetAmount || targetAmount <= 0) {
            Stand120.showNotification('Please enter a valid target amount', 'error');
            return;
        }

        Stand120.ajax('get_target_recommendation', {
            target_amount: targetAmount
        }).then(response => {
            if (response.success) {
                const data = response.data;
                $('#targetResult').show();

                const statusText = data.achievable
                    ? '<span style="color: #4caf50;">✓ Target is achievable</span>'
                    : '<span style="color: #ff9800;">⚠ Target may not be fully achievable with current products</span>';
                $('#targetSummary').html(
                    `Target: ₦${Stand120.formatNumber(data.target)} | ` +
                    `Projected Total: ₦${Stand120.formatNumber(data.total_projected)} | ` +
                    statusText
                );

                const $tbody = $('#targetRecommendationTable tbody').empty();
                if (data.recommendations && data.recommendations.length > 0) {
                    data.recommendations.forEach(rec => {
                        $tbody.append(`<tr>
                            <td>${rec.product_name}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(rec.price)}</td>
                            <td>${rec.suggested_qty}</td>
                            <td>${rec.avg_daily_sales}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(rec.projected_revenue)}</td>
                        </tr>`);
                    });
                } else {
                    $tbody.append('<tr><td colspan="5" style="text-align:center;color:var(--text-muted)">No products available</td></tr>');
                }
            } else {
                Stand120.showNotification(response.data?.message || 'Failed to calculate', 'error');
            }
        });
    }

    function loadAnalytics() {
        Stand120.ajax('get_analytics', {
            type: 'overview',
            period: $('#periodFilter').val(),
            date: $('#periodDate').val()
        }).then(response => {
            if (response.success) {
                const data = response.data.analytics;

                // Update overview cards
                const totalSales = parseFloat(data.total_sales) || 0;
                const totalOrders = parseInt(data.total_orders) || 0;
                const avgOrder = totalOrders > 0 ? totalSales / totalOrders : 0;
                const revenue = parseFloat(data.revenue) || 0;
                const profit = parseFloat(data.profit) || 0;
                const totalAllExpenses = parseFloat(data.total_all_expenses) || 0;
                const loss = parseFloat(data.loss) || 0;

                $('#revenueAnalytics').text('₦' + Stand120.formatNumber(revenue));
                $('#profitAnalytics').text('₦' + Stand120.formatNumber(profit)).css('color', profit >= 0 ? '#4caf50' : '#f44336');
                $('#expensesAnalytics').text('₦' + Stand120.formatNumber(totalAllExpenses));
                $('#lossAnalytics').text('₦' + Stand120.formatNumber(loss)).css('color', loss > 0 ? '#f44336' : 'inherit');

                $('#totalSalesAnalytics').text('₦' + Stand120.formatNumber(totalSales));
                $('#totalOrdersAnalytics').text(Stand120.formatNumber(totalOrders));
                $('#avgOrderAnalytics').text('₦' + Stand120.formatNumber(avgOrder));

                // Render product revenue attribution
                const $revenueBody = $('#productRevenueTable tbody').empty();
                if (data.product_revenue && data.product_revenue.length > 0) {
                    data.product_revenue.forEach(product => {
                        $revenueBody.append(`<tr>
                            <td>${product.product_name}</td>
                            <td>${Stand120.formatNumber(product.qty_sold)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(product.unit_price || 0)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(product.revenue)}</td>
                        </tr>`);
                    });
                } else {
                    $revenueBody.append('<tr><td colspan="4" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render detailed expenses breakdown
                const $expBody = $('#detailedExpensesTable tbody').empty();
                if (data.detailed_expenses && data.detailed_expenses.length > 0) {
                    data.detailed_expenses.forEach(exp => {
                        $expBody.append(`<tr>
                            <td>${exp.description}</td>
                            <td>${Stand120.formatNumber(exp.total_qty)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(exp.total_amount)}</td>
                        </tr>`);
                    });
                } else {
                    $expBody.append('<tr><td colspan="3" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render staff performance
                const $staffBody = $('#staffPerformanceTable tbody').empty();
                if (data.staff_performance && data.staff_performance.length > 0) {
                    data.staff_performance.forEach(staff => {
                        $staffBody.append(`<tr>
                            <td>${staff.full_name}</td>
                            <td>${Stand120.formatNumber(staff.order_count || 0)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(staff.total_sales || 0)}</td>
                        </tr>`);
                    });
                } else {
                    $staffBody.append('<tr><td colspan="3" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render order preparation analytics
                const prepSummary = data.preparation?.summary || {};
                $('#prepSummary').text(
                    `Total Added: ${Stand120.formatNumber(prepSummary.total_added || 0)} | ` +
                    `Total Sold: ${Stand120.formatNumber(prepSummary.total_sold || 0)} | ` +
                    `Closing: ${Stand120.formatNumber(prepSummary.closing_value || 0)}`
                );

                const $prepBody = $('#prepAnalyticsTable tbody').empty();
                if (data.preparation?.records?.length) {
                    data.preparation.records.forEach(record => {
                        $prepBody.append(`<tr>
                            <td>${record.product_name}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.total_added || 0)}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.total_sold || 0)}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.closing_value || 0)}</td>
                        </tr>`);
                    });
                } else {
                    $prepBody.append('<tr><td colspan="4" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render stock inventory analytics
                const stockSummary = data.stock?.summary || {};
                $('#stockSummary').text(
                    `Added: ${Stand120.formatNumber(stockSummary.added_packs || 0)} | ` +
                    `Used: ${Stand120.formatNumber(stockSummary.used_packs || 0)} | ` +
                    `Closing: ${Stand120.formatNumber(stockSummary.closing_packs || 0)}`
                );

                const $stockBody = $('#stockAnalyticsTable tbody').empty();
                if (data.stock?.records?.length) {
                    data.stock.records.forEach(record => {
                        $stockBody.append(`<tr>
                            <td>${record.product_name}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.added_packs || 0)}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.used_packs || 0)}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.closing_packs || 0)}</td>
                        </tr>`);
                    });
                } else {
                    $stockBody.append('<tr><td colspan="4" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render chopping inventory analytics
                const chopSummary = data.chopping?.summary || {};
                $('#chopSummary').text(
                    `Imported: ${Stand120.formatNumber(chopSummary.import_whole || 0)} | ` +
                    `Prepared: ${Stand120.formatNumber(chopSummary.prepared_whole || 0)} | ` +
                    `Packs: ${Stand120.formatNumber(chopSummary.packs_gotten || 0)}`
                );

                const $chopBody = $('#chopAnalyticsTable tbody').empty();
                if (data.chopping?.records?.length) {
                    data.chopping.records.forEach(record => {
                        $chopBody.append(`<tr>
                            <td>${record.product_name}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.import_whole || 0)}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.prepared_whole || 0)}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.packs_gotten || 0)}</td>
                        </tr>`);
                    });
                } else {
                    $chopBody.append('<tr><td colspan="4" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render import records analytics
                const importSummary = data.imports?.summary || {};
                $('#importSummary').text(
                    `Total Imported: ${Stand120.formatNumber(importSummary.quantity_imported || 0)}`
                );

                const $importBody = $('#importAnalyticsTable tbody').empty();
                if (data.imports?.records?.length) {
                    data.imports.records.forEach(record => {
                        $importBody.append(`<tr>
                            <td>${record.product_name}</td>
                            <td>${record.product_type}</td>
                            <td class="formatted-number">${Stand120.formatNumber(record.quantity_imported || 0)}</td>
                        </tr>`);
                    });
                } else {
                    $importBody.append('<tr><td colspan="3" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render financial summary analytics
                const financials = data.financials || {};
                const $financialBody = $('#financialAnalyticsTable tbody').empty();
                $financialBody.append(`<tr>
                    <td class="formatted-number">₦${Stand120.formatNumber(financials.total_sales || 0)}</td>
                    <td class="formatted-number">₦${Stand120.formatNumber(financials.cash_sales || 0)}</td>
                    <td class="formatted-number">₦${Stand120.formatNumber(financials.transfer_sales || 0)}</td>
                    <td class="formatted-number">₦${Stand120.formatNumber(financials.delivery_fees || 0)}</td>
                    <td class="formatted-number">₦${Stand120.formatNumber(financials.extras_amount || 0)}</td>
                    <td class="formatted-number">₦${Stand120.formatNumber(financials.expenses_amount || 0)}</td>
                </tr>`);

                // Render top products
                const $productsBody = $('#topProductsTable tbody').empty();
                if (data.top_products && data.top_products.length > 0) {
                    data.top_products.forEach(product => {
                        $productsBody.append(`<tr>
                            <td>${product.product_name}</td>
                            <td>${Stand120.formatNumber(product.qty_sold)}</td>
                            <td class="formatted-number">₦${Stand120.formatNumber(product.revenue)}</td>
                        </tr>`);
                    });
                } else {
                    $productsBody.append('<tr><td colspan="3" style="text-align:center;color:var(--text-muted)">No data</td></tr>');
                }

                // Render simple bar chart for daily sales
                const $chartContainer = $('#dailySalesChart').empty();
                if (data.daily_sales && data.daily_sales.length > 0) {
                    const maxSales = Math.max(...data.daily_sales.map(d => parseFloat(d.total) || 0));

                    data.daily_sales.forEach(day => {
                        const height = maxSales > 0 ? ((parseFloat(day.total) || 0) / maxSales * 150) : 0;
                        const date = new Date(day.order_date);
                        const label = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

                        $chartContainer.append(`
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1; min-width: 40px; max-width: 60px;">
                                <div style="width: 100%; height: ${Math.max(height, 5)}px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); border-radius: 4px 4px 0 0;" title="₦${Stand120.formatNumber(day.total)}"></div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); margin-top: 4px; text-align: center;">${label}</span>
                            </div>
                        `);
                    });
                } else {
                    $chartContainer.html('<p style="color: var(--text-muted); text-align: center; width: 100%;">No sales data for this period</p>');
                }
            }
        });
    }
</script>

<?php include STAND120_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
