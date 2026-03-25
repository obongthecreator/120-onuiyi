<?php
/**
 * Admin Panel Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check admin access
if (!Stand120_Auth::is_admin()) {
    wp_redirect(home_url('/120-stand/'));
    exit;
}

$page_title = 'Admin Panel - 120 Stand Inventory';
$current_user = Stand120_Auth::get_current_user_data();

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
            <span>Administrator</span>
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
    <i class="fas fa-cog"></i>
    Admin Panel
</h1>

<!-- Tabs -->
<div class="tabs">
    <button class="tab-btn active" data-tab="products-tab">
        <i class="fas fa-box"></i> Products
    </button>
    <button class="tab-btn" data-tab="staff-tab">
        <i class="fas fa-users"></i> Staff
    </button>
    <button class="tab-btn" data-tab="opening-tab">
        <i class="fas fa-edit"></i> Opening Values
    </button>
    <button class="tab-btn" data-tab="settings-tab">
        <i class="fas fa-sliders-h"></i> Settings
    </button>
</div>

<!-- Products Tab -->
<div id="products-tab" class="tab-content active">
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--primary-color);">
                <i class="fas fa-box-open"></i> Manage Products
            </h3>
            <div class="admin-actions">
                <button id="addProduct" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Product
                </button>
                <button id="saveProducts" class="btn btn-success btn-sm">
                    <i class="fas fa-save"></i> Save All
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table" id="productsTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price (₦)</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Staff Tab -->
<div id="staff-tab" class="tab-content">
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--primary-color);">
                <i class="fas fa-user-friends"></i> Manage Staff
            </h3>
            <button id="addStaff" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add Staff
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table" id="staffTable">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Opening Values Tab -->
<div id="opening-tab" class="tab-content">
    <div class="glass-card">
        <h3 style="margin-bottom: 20px; color: var(--primary-color);">
            <i class="fas fa-edit"></i> Set Opening Values
        </h3>
        
        <p style="color: var(--text-muted); margin-bottom: 20px;">
            <i class="fas fa-info-circle"></i> 
            Set initial opening values for inventory tracking. These values will be used as starting points.
        </p>
        
        <div class="form-group">
            <label class="form-label">Select Date</label>
            <input type="date" id="openingDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="max-width: 200px;">
        </div>
        
        <!-- Order Preparation Opening -->
        <div class="admin-section">
            <h4 class="admin-section-title">Order Preparation Opening Values</h4>
            <div class="table-responsive">
                <table class="table" id="prepOpeningTable">
                    <thead>
                        <tr>
                            <th>Fruit</th>
                            <th>Opening Value (Cup/Bottle)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Stock Inventory Opening -->
        <div class="admin-section">
            <h4 class="admin-section-title">Stock Inventory Opening Values</h4>
            <div class="table-responsive">
                <table class="table" id="stockOpeningTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Opening Packs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Chopping Inventory Opening -->
        <div class="admin-section">
            <h4 class="admin-section-title">Chopping Inventory Opening Values</h4>
            <div class="table-responsive">
                <table class="table" id="chopOpeningTable">
                    <thead>
                        <tr>
                            <th>Fruit</th>
                            <th>Opening (Whole)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <button id="saveOpeningValues" class="btn btn-success">
            <i class="fas fa-save"></i> Save Opening Values
        </button>
    </div>
</div>

<!-- Settings Tab -->
<div id="settings-tab" class="tab-content">
    <div class="glass-card">
        <h3 style="margin-bottom: 20px; color: var(--primary-color);">
            <i class="fas fa-sliders-h"></i> System Settings
        </h3>
        
        <div class="form-group">
            <label class="form-label">Business Name</label>
            <input type="text" class="form-control" value="120 Stand" readonly>
        </div>
        
        <div class="form-group">
            <label class="form-label">Currency Symbol</label>
            <input type="text" class="form-control" value="₦" readonly>
        </div>
        
        <div class="form-group">
            <label class="form-label">Day Reset Time</label>
            <input type="text" class="form-control" value="11:59 PM" readonly>
            <small style="color: var(--text-muted);">Inventory values reset to new day after this time</small>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-glass);">
            <h4 style="color: var(--text-secondary); margin-bottom: 16px;">Data Management</h4>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button id="exportData" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Export Data
                </button>
                <button id="clearCache" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Clear Cache
                </button>
                <button id="clearAllRecords" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Clear All Records
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if (typeof AdminPanel !== 'undefined') {
            AdminPanel.init();
        }
    });
</script>

<?php include STAND120_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
