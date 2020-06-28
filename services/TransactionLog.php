<?php

class TransactionLog
{

    private $version;

    public function __construct()
    {
    }

    public function activate()
    {
        global $wpdb;
        $table_name = $this->getTableName();
        $sql = "CREATE TABLE $table_name (
                     ID INT NOT NULL AUTO_INCREMENT,
                    `reference` VARCHAR(50) NOT NULL,
                    `type` VARCHAR(50),
                    `gateway` VARCHAR(20),
                    `response` MEDIUMTEXT NOT NULL,
                    `response_code` INT(2),
                    `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (ID)
            )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        add_option('tl_db_version', $this->getVersion());
    }

    public function getAllLogs()
    {
    }

    public function addLog($log)
    {
        global $wpdb;
        return $wpdb->insert($this->getTableName(), $log);
    }

    public function onTransaction($reference, $gateway, $type, $response){
        $responseCode = json_decode($response)->responseCode;
        $log = [
            'reference' => $reference,
            'gateway' => $gateway,
            'type' => $type,
            'response' => $response,
            'response_code' => $responseCode
        ];
        $this->addLog($log);
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    private function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . "paga_transaction_logs";
    }
}
