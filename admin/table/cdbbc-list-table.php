<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Cdbbc_donation_list extends WP_List_Table
{

    public function get_columns()
    {
        $columns = array(

            'id' => '#',
            'transaction_id' => __("Transaction Id", "cdbbc"),
            'sender' => __("Sender", "cdbbc"),
            'recever' => __("Reciever", "cdbbc"),
            'currency' => __("Currency", "cdbbc"),
            'amount' => __("Amount", "cdbbc"),
            'wallet_name' => __("Wallet", "cdbbc"),
            'network' => __("Network", "cdbbc"),
            'payment_status' => __("Payment Status", "cdbbc"),
            'user_email' => __("Email", "cdbbc"),
            'transaction_status' => __("Transaction Status", "cdbbc"),
            'blockchain' => __("Blockchain", "cdbbc"),
            'last_updated' => __("Last Updated", "cdbbc"),
        );
        return $columns;
    }

    public function prepare_items()
    {
        global $wpdb, $_wp_column_headers;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $query = 'SELECT * FROM ' . $wpdb->base_prefix . 'cdbbc_transaction';

        $user_search_keyword = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';

        if (isset($user_search_keyword) && !empty($user_search_keyword)) {
            $query .= $wpdb->prepare(' WHERE ( user_email LIKE %s OR selected_network LIKE %s OR currency LIKE %s)', "%{$user_search_keyword}%", "%{$user_search_keyword}%", "%{$user_search_keyword}%");
        }

// Ordering parameters
        $allowed_order_columns = array('id', 'last_updated');
        $allowed_order_direction = array('ASC', 'DESC');
        $orderby = (in_array($_REQUEST["orderby"], $allowed_order_columns)) ? esc_sql($_REQUEST["orderby"]) : 'last_updated';
        $order = (in_array($_REQUEST["order"], $allowed_order_direction)) ? esc_sql($_REQUEST["order"]) : 'DESC';
        if (!empty($orderby) & !empty($order)) {
            $query .= $wpdb->prepare(' ORDER BY %s %s', $orderby, $order);
        }

// Pagination parameters
        $totalitems = $wpdb->query($query);
        $perpage = 10;
        if (!is_numeric($perpage) || empty($perpage)) {
            $perpage = 10;
        }

        $paged = !empty($_REQUEST["paged"]) ? esc_sql($_REQUEST["paged"]) : false;

        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);

        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query .= $wpdb->prepare(' LIMIT %d, %d', $offset, $perpage);
        }

// Register the pagination & build link
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        )
        );

// Get feedback data from database
        $this->items = $wpdb->get_results($query);

    }

    public function column_default($item, $column_name)
    {

        switch ($column_name) {

            case 'id':
                return $item->id;
            case 'transaction_id':
                return $item->transaction_id;

            case 'sender':
                return $item->sender;
            case 'recever':
                return $item->recever;
            case 'currency':
                return $item->currency;
            case 'amount':
                return $item->amount;
            case 'wallet_name':
                return $item->wallet_name;
            case 'network':
                return $item->selected_network;
            case 'payment_status':
                return $item->payment_status;
            case 'user_email':
                return $item->user_email;
            case 'transaction_status':
                return $item->transaction_status;
            case 'blockchain':
                return $item->blockchain;
            case 'last_updated':
                return $this->timeAgo($item->last_updated);
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'id' => array('id', true),
            'last_updated' => array('last_updated', true),
        );
        return $sortable_columns;
    }

    public function timeAgo($time_ago)
    {
        $time_ago = strtotime($time_ago) ? strtotime($time_ago) : $time_ago;
        $time = time() - $time_ago;
        switch ($time):
// seconds
    case $time < 60:
        return '1 minute ago';
// minutes
    case $time >= 60 && $time < 3600:
        return (round($time / 60) == 1) ? '1 minute' : round($time / 60) . ' minutes ago';
// hours
    case $time >= 3600 && $time < 86400:
        return (round($time / 3600) == 1) ? '1 hour ago' : round($time / 3600) . ' hours ago';
// days
    case $time >= 86400:
        return (round($time / 86400) == 1) ? date_i18n('M j, Y', $time_ago) : date_i18n('M j, Y', $time_ago);

        endswitch;
    }

}
