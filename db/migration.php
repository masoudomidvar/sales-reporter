<?php

ini_set('display_errors', 1);

require_once('database-handler.php');

(new Migration())->migrate();

class Migration
{
    protected $databaseHandler;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler(true);
    }

    public function migrate(): void
    {
        if (!$this->databaseHandler->databaseExists()) {
            $this->createDatabase();

            $this->databaseHandler->connectToDatabae();

            $this->createTables();
        }

        echo "Migration done.";
    }

    protected function createDatabase(): void
    {
        $dbName = $this->databaseHandler->dbName;
        $query = "CREATE DATABASE $dbName";

        $this->databaseHandler->executeQuery($query);
    }

    protected function createTables(): void
    {
        $query = "CREATE TABLE customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100)
        );

        CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            price DECIMAL(10, 2)
        );

        CREATE TABLE sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT,
            product_id INT,
            price DECIMAL(10, 2),
            sale_date DATETIME,
            INDEX idx_sale_customer_product_price (customer_id, product_id, price)
        );";

        $this->databaseHandler->executeQuery($query);
    }
}
