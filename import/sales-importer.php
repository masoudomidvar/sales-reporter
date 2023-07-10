<?php

ini_set('display_errors', 1);

require_once('../db/database-handler.php');
require_once('../db/repository.php');

(new SalesImporter())->import();

class SalesImporter
{
    protected $databaseHandler;
    protected $repository;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler();
        $this->repository = new Repository();
    }

    public function import(): void
    {
        $data = $this->getJsonData();

        $this->saveData($data);
    }

    protected function getJsonData(): array
    {
        $jsonData = file_get_contents('sales.json');

        return json_decode($jsonData, true);
    }

    protected function saveData(array $data): void
    {
        foreach ($data as $entry) {
            // Check if the customer already exists in the database
            $customerId = $this->repository->getCustomerIdByEmail($entry['customer_mail']);
            if (!$customerId) {
                // Insert the customer into the customers table
                $customerId = $this->repository->createCustomer($entry['customer_name'], $entry['customer_mail']);
            }

            // Check if the product already exists in the database
            $productId = $this->repository->getProductIdByName($entry['product_name']);

            if (!$productId) {
                // Insert the product into the products table
                $productId = $this->repository->createProduct($entry['product_name'], $entry['product_price']);
            }

            // Insert the sale into the sales table
            $this->repository->createSales($customerId, $productId, $entry['product_price'], $entry['sale_date']);
        }

        echo "Import done.";
    }
}
