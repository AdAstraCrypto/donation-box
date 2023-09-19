<?php

class CDBBC_database
{

    /**
     * Get things started
     *
     * @access  public
     * @since   1.0
     */
    public function __construct()
    {

        global $wpdb;

        $this->table_name = $wpdb->base_prefix . 'cdbbc_transaction';
        $this->primary_key = 'id';
        $this->version = '1.0';

    }

    public function cdbbc_insert($transactions)
    {
        if (is_array($transactions) && count($transactions) >= 1) {

            return $this->wp_insert_rows($transactions, $this->table_name, true, 'transaction_id');
        }
    }

    public function wp_insert_rows($row_arrays, $wp_table_name, $update = false, $primary_key = null)
    {
        global $wpdb;
    
        if (!isset($wpdb)) {
            exit("The $wpdb variable is not defined. Please make sure the WordPress database object is available.");
        }
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        $table_name = $wpdb->base_prefix . 'cdbbc_transaction';

        $is_transaction_status = $wpdb->get_results("SELECT transaction_status FROM $table_name");
        $is_blockchain = $wpdb->get_results("SELECT blockchain FROM $table_name");
        error_log(["is_transaction_status" =>$is_transaction_status]);
        if (empty($is_transaction_status) || empty($is_blockchain)) {
            error_log("empty transaction_status or blockchain");
            $temp_table_name = $wpdb->base_prefix . 'temp_cdbbc_transaction';
            $wpdb->query("CREATE TABLE $temp_table_name LIKE $table_name");
            $wpdb->query("INSERT INTO $temp_table_name SELECT * FROM $table_name");
            $wpdb->query("DELETE FROM $table_name");
            if(empty($is_transaction_status)){
                $wpdb->query("ALTER TABLE $table_name ADD transaction_status varchar(100) NOT NULL");
            }
            if(empty($is_blockchain)){
                $wpdb->query("ALTER TABLE $table_name ADD blockchain varchar(100) NOT NULL");
            }
            $sessions = $wpdb->get_results(sprintf("SELECT * FROM $temp_table_name"));
            if ($sessions) {

                foreach ($sessions as $session) {
                    try{
                        $reinserted = $wpdb->insert(
                            $table_name,
                            array(
                                'id' => $session->id,
                                'transaction_id' => $session->transaction_id,
                                'sender' => $session->sender,
                                'recever' => $session->recever,
                                'currency' => $session->currency,
                                'amount' => $session->amount,
                                'wallet_name' => $session->wallet_name,
                                'selected_network' => $session->selected_network,
                                'payment_status' => $session->payment_status,
                                'user_name' => $session->user_name,
                                'user_email' => $session->user_email,
                                'ip_address' => $session->ip_address,
                                'transaction_status' => $session->payment_status,
                                'blockchain' => $session->selected_network,
                                'last_updated' => $session->last_updated,
                            )
                        );
                    }
                    catch ( Exception $e) { 
                        $log->error('Message: ' .$e->getMessage());
                    }
                $wpdb->query("drop table $temp_table_name");
                }
            }
        }
        $wp_table_name = esc_sql($wp_table_name);
        // Setup arrays for Actual Values, and Placeholders
        $values = array();
        $place_holders = array();
        $query = "";
        $query_columns = "";
        // $floatCols = array('price', 'percent_change_24h', 'percent_change_1y', 'percent_change_30d', 'percent_change_7d', 'market_cap', 'total_volume', 'circulating_supply', 'ath', 'ath_change_percentage', 'high_24h', 'low_24h');

        $query .= "INSERT INTO `{$wp_table_name}` (";

        foreach ($row_arrays as $key => $value) {
            // foreach ($row_array as $key => $value) {
            //  if ($key == 0) {
            if ($query_columns) {
                $query_columns .= ", " . $key . "";
            } else {
                $query_columns .= "" . $key . "";
            }
            //  }

            $values[] = $value;

            $symbol = "%s";
            /*     if (is_numeric($value)) {
            $symbol = "%d";
            }

            if (in_array($key, $floatCols)) {
            $symbol = "%f";
            } */
            if (isset($place_holders[$key])) {
                $place_holders[$key] .= ", '$symbol'";
            } else {
                $place_holders[$key] = "( '$symbol'";
            }
            // }
            // mind closing the GAP
            $place_holders[$key] .= ")";
        }
        // $place_holders[] .= ")";

        $query .= " $query_columns ) VALUES (";

        $query .= implode(', ', $place_holders) . ')';

        if ($update) {
            $update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
            $cnt = 0;

            foreach ($row_arrays as $key => $value) {
                if ($cnt == 0) {
                    $update .= "$key=VALUES($key)";
                    $cnt = 1;
                } else {
                    $update .= ", $key=VALUES($key)";
                }
            }
            $query .= $update;
        }
        $sql = $wpdb->prepare($query, $values);

        if ($wpdb->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function create_table()
    {

        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        //IF NOT EXISTS - condition not required

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->base_prefix . 'cdbbc_transaction' . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
        transaction_id varchar(100) NOT NULL UNIQUE,
        sender varchar(100) NOT NULL,
        recever varchar(100) NOT NULL,
        currency varchar(20) NOT NULL,
        amount varchar(30) NOT NULL,
        wallet_name varchar(100) NOT NULL,
        selected_network varchar(50) NOT NULL,
        payment_status varchar(30) NOT NULL,
        user_name varchar(50) NOT NULL,
        user_email varchar(50) NOT NULL,
        ip_address varchar(100) NOT NULL,
        transaction_status varchar(100) NOT NULL,
        blockchain varchar(100) NOT NULL,
        last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

        dbDelta($sql);

        update_option($this->table_name . '_db_version', $this->version);
    }

    /**
     * Remove table linked to this database class file
     */
    public function drop_table()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . $this->table_name);
    }

}
