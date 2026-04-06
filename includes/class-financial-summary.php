<?php
/**
 * Financial Summary Class
 * Handles daily financial summaries
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stand120_Financial_Summary {
    
    /**
     * Save financial summary
     */
    public static function save($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'stand120_financial_summary';
        
        $date = sanitize_text_field($data['date'] ?? date('Y-m-d'));
        $extras_amount = floatval($data['extras_amount'] ?? 0);
        $extras_remark = sanitize_textarea_field($data['extras_remark'] ?? '');
        $expenses_amount = floatval($data['expenses_amount'] ?? 0);
        $expenses_remark = sanitize_textarea_field($data['expenses_remark'] ?? '');
        $staff_id = Stand120_Auth::get_current_staff_id();
        
        // Market Card Expense Left comes from stand120_expenses table (not in formula)
        $market_card_expense = Stand120_Expense_Record::get_total_for_date($date);
        
        // Get existing record
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE summary_date = %s",
            $date
        ));
        
        if ($existing) {
            // Recalculate cash left: only Expense is subtracted (NOT market_card_expense)
            $cash_left = ($existing->cash_sales + $existing->old_cash + $extras_amount) - $expenses_amount;
            
            // Update existing
            $wpdb->update($table, array(
                'extras_amount' => $extras_amount,
                'extras_remark' => $extras_remark,
                'expenses_amount' => $expenses_amount,
                'expenses_remark' => $expenses_remark,
                'market_card_expense' => $market_card_expense,
                'cash_left' => $cash_left,
                'staff_id' => $staff_id
            ), array('id' => $existing->id));
            
            return array(
                'success' => true,
                'message' => 'Financial summary updated',
                'data' => self::get_for_date($date)
            );
        } else {
            // Get yesterday's cash left as today's old cash
            $yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
            $yesterday_record = $wpdb->get_row($wpdb->prepare(
                "SELECT cash_left FROM $table WHERE summary_date = %s",
                $yesterday
            ));
            $old_cash = $yesterday_record ? floatval($yesterday_record->cash_left) : 0;
            
            // Get today's totals from orders
            $orders_table = $wpdb->prefix . 'stand120_orders';
            $totals = $wpdb->get_row($wpdb->prepare(
                "SELECT 
                    SUM(grand_total) as total_sales,
                    SUM(transfer_amount) as transfer_sales,
                    SUM(cash_amount) as cash_sales,
                    SUM(delivery_fee) as delivery_fees
                FROM $orders_table
                WHERE order_date = %s",
                $date
            ));
            
            $total_sales = floatval($totals->total_sales ?? 0);
            $transfer_sales = floatval($totals->transfer_sales ?? 0);
            $cash_sales = floatval($totals->cash_sales ?? 0);
            $delivery_fees = floatval($totals->delivery_fees ?? 0);
            
            $cash_left = ($cash_sales + $old_cash + $extras_amount) - $expenses_amount;
            
            // Insert new
            $wpdb->insert($table, array(
                'summary_date' => $date,
                'total_sales' => $total_sales,
                'transfer_sales' => $transfer_sales,
                'cash_sales' => $cash_sales,
                'delivery_fees' => $delivery_fees,
                'extras_amount' => $extras_amount,
                'extras_remark' => $extras_remark,
                'expenses_amount' => $expenses_amount,
                'expenses_remark' => $expenses_remark,
                'market_card_expense' => $market_card_expense,
                'old_cash' => $old_cash,
                'cash_left' => $cash_left,
                'staff_id' => $staff_id
            ));
            
            return array(
                'success' => true,
                'message' => 'Financial summary created',
                'data' => self::get_for_date($date)
            );
        }
    }
    
    /**
     * Update sales totals (called after each order)
     */
    public static function update_sales_totals($date) {
        global $wpdb;
        $table = $wpdb->prefix . 'stand120_financial_summary';
        $orders_table = $wpdb->prefix . 'stand120_orders';
        
        // Get today's totals from orders
        $totals = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                SUM(grand_total) as total_sales,
                SUM(transfer_amount) as transfer_sales,
                SUM(cash_amount) as cash_sales,
                SUM(delivery_fee) as delivery_fees
            FROM $orders_table
            WHERE order_date = %s",
            $date
        ));
        
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE summary_date = %s",
            $date
        ));
        
        if ($existing) {
            $cash_left = (floatval($totals->cash_sales ?? 0) + $existing->old_cash + $existing->extras_amount) - $existing->expenses_amount;
            
            $wpdb->update($table, array(
                'total_sales' => floatval($totals->total_sales ?? 0),
                'transfer_sales' => floatval($totals->transfer_sales ?? 0),
                'cash_sales' => floatval($totals->cash_sales ?? 0),
                'delivery_fees' => floatval($totals->delivery_fees ?? 0),
                'cash_left' => $cash_left
            ), array('id' => $existing->id));
        }
    }
    
    /**
     * Get financial summary for a date
     */
    public static function get_for_date($date) {
        global $wpdb;
        $table = $wpdb->prefix . 'stand120_financial_summary';
        $orders_table = $wpdb->prefix . 'stand120_orders';
        
        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE summary_date = %s",
            $date
        ));
        
        if ($record) {
            // Also get the latest market card expense from expenses table
            $market_card_expense_from_table = Stand120_Expense_Record::get_total_for_date($date);
            
            return array(
                'date' => $date,
                'total_sales' => floatval($record->total_sales),
                'transfer_sales' => floatval($record->transfer_sales),
                'cash_sales' => floatval($record->cash_sales),
                'delivery_fees' => floatval($record->delivery_fees),
                'extras_amount' => floatval($record->extras_amount),
                'extras_remark' => $record->extras_remark,
                'expenses_amount' => floatval($record->expenses_amount),
                'expenses_remark' => $record->expenses_remark,
                'market_card_expense' => $market_card_expense_from_table > 0 ? $market_card_expense_from_table : floatval($record->market_card_expense ?? 0),
                'old_cash' => floatval($record->old_cash),
                'cash_left' => floatval($record->cash_left)
            );
        }
        
        // No record exists, calculate from orders
        $totals = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                SUM(grand_total) as total_sales,
                SUM(transfer_amount) as transfer_sales,
                SUM(cash_amount) as cash_sales,
                SUM(delivery_fee) as delivery_fees
            FROM $orders_table
            WHERE order_date = %s",
            $date
        ));
        
        // Get yesterday's cash left
        $yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
        $yesterday_record = $wpdb->get_row($wpdb->prepare(
            "SELECT cash_left FROM $table WHERE summary_date = %s",
            $yesterday
        ));
        $old_cash = $yesterday_record ? floatval($yesterday_record->cash_left) : 0;
        
        $cash_sales = floatval($totals->cash_sales ?? 0);
        $market_card_expense = Stand120_Expense_Record::get_total_for_date($date);
        
        return array(
            'date' => $date,
            'total_sales' => floatval($totals->total_sales ?? 0),
            'transfer_sales' => floatval($totals->transfer_sales ?? 0),
            'cash_sales' => $cash_sales,
            'delivery_fees' => floatval($totals->delivery_fees ?? 0),
            'extras_amount' => 0,
            'extras_remark' => '',
            'expenses_amount' => 0,
            'expenses_remark' => '',
            'market_card_expense' => $market_card_expense,
            'old_cash' => $old_cash,
            'cash_left' => $cash_sales + $old_cash
        );
    }
    
    /**
     * Self-heal financial summary records with wrong calculations.
     * Recalculates cash_left for all records in the given date range
     * and updates any that don't match the formula:
     * Cash Left = (Cash Sales + Old Cash + Extras) - Expense
     */
    public static function recalculate_records($date_from = null, $date_to = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'stand120_financial_summary';
        
        $sql = "SELECT * FROM $table WHERE 1=1";
        $params = array();
        
        if (!empty($date_from)) {
            $sql .= " AND summary_date >= %s";
            $params[] = $date_from;
        }
        if (!empty($date_to)) {
            $sql .= " AND summary_date <= %s";
            $params[] = $date_to;
        }
        
        $sql .= " ORDER BY summary_date ASC";
        
        if (!empty($params)) {
            $records = $wpdb->get_results($wpdb->prepare($sql, $params));
        } else {
            $records = $wpdb->get_results($sql);
        }
        
        $fixed = 0;
        
        foreach ($records as $record) {
            $correct_cash_left = (floatval($record->cash_sales) + floatval($record->old_cash) + floatval($record->extras_amount)) - floatval($record->expenses_amount);
            
            // Refresh market_card_expense from expenses table only if there are actual expense records
            $live_mce = Stand120_Expense_Record::get_total_for_date($record->summary_date);
            $stored_mce = floatval($record->market_card_expense ?? 0);
            
            // Only overwrite market_card_expense if the live value is > 0,
            // or if the stored value is 0 (nothing to lose). This preserves old stored values
            // when expense records have been deleted from the expenses table.
            $final_mce = ($live_mce > 0) ? $live_mce : $stored_mce;
            
            $stored_cash_left = floatval($record->cash_left);
            
            // Fix if cash_left is wrong or market_card_expense needs updating
            $needs_fix = abs($stored_cash_left - $correct_cash_left) > 0.01;
            $mce_changed = abs($stored_mce - $final_mce) > 0.01;
            
            if ($needs_fix || $mce_changed) {
                $wpdb->update($table, array(
                    'cash_left' => $correct_cash_left,
                    'market_card_expense' => $final_mce
                ), array('id' => $record->id));
                $fixed++;
            }
        }
        
        return $fixed;
    }
    
    /**
     * Get financial summary history
     */
    public static function get_history($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'stand120_financial_summary';
        $staff_table = $wpdb->prefix . 'stand120_staff';
        
        // Self-heal: recalculate any records with wrong cash_left before returning
        self::recalculate_records(
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null
        );
        
        $sql = "SELECT fs.*, s.full_name as staff_name
                FROM $table fs
                LEFT JOIN $staff_table s ON fs.staff_id = s.id
                WHERE 1=1";
        $params = array();
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND fs.summary_date >= %s";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND fs.summary_date <= %s";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY fs.summary_date DESC";
        
        // Pagination
        $page = max(1, intval($filters['page'] ?? 1));
        $per_page = max(1, min(100, intval($filters['per_page'] ?? 20)));
        $offset = ($page - 1) * $per_page;
        
        // Get total count
        $count_sql = str_replace("SELECT fs.*, s.full_name as staff_name", "SELECT COUNT(*)", $sql);
        if (!empty($params)) {
            $total = $wpdb->get_var($wpdb->prepare($count_sql, $params));
        } else {
            $total = $wpdb->get_var($count_sql);
        }
        
        $sql .= " LIMIT %d OFFSET %d";
        $params[] = $per_page;
        $params[] = $offset;
        
        if (!empty($params)) {
            $records = $wpdb->get_results($wpdb->prepare($sql, $params));
        } else {
            $records = $wpdb->get_results($sql);
        }
        
        return array(
            'records' => $records,
            'total' => intval($total),
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        );
    }
}
