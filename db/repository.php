<?php

ini_set('display_errors', 1);

require_once('database-handler.php');

class Repository
{
    protected $databaseHandler;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler();
    }

    public function getCustomers(): IteratorAggregate
    {
        $query = "SELECT * FROM customers";

        return $this->databaseHandler->executeQuery($query);
    }

    public function getProducts(): IteratorAggregate
    {
        $query = "SELECT * FROM products";

        return $this->databaseHandler->executeQuery($query);
    }

    public function getFilteredSales(array $data): array
    {
        $query = "SELECT sales.*, customers.name AS customer_name, products.name AS product_name
            FROM sales
            INNER JOIN customers ON sales.customer_id = customers.id
            INNER JOIN products ON sales.product_id = products.id
            WHERE sales.product_id = :product_id
            AND sales.customer_id = :customer_id
            AND sales.price = :price";

        $statement = $this->databaseHandler->dbConnection->prepare($query);
        $statement->execute([
            'product_id' => $data['product'],
            'customer_id' => $data['customer'],
            'price' => $data['price']
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerIdByEmail(string $email): int|bool
    {
        $statement = $this->databaseHandler->dbConnection->prepare("SELECT id FROM customers WHERE email = ?");
        $statement->execute([$email]);

        return $statement->fetchColumn();
    }

    public function createCustomer(string $name, string $email): int
    {
        $statement = $this->databaseHandler->dbConnection->prepare("INSERT INTO customers (name, email) VALUES (?, ?)");
        $statement->execute([$name, $email]);

        return $this->databaseHandler->dbConnection->lastInsertId();
    }

    public function getProductIdByName(string $name): int|bool
    {
        $statement = $this->databaseHandler->dbConnection->prepare("SELECT id FROM products WHERE name = ?");
        $statement->execute([$name]);

        return $statement->fetchColumn();
    }

    public function createProduct(string $name, int $price): int
    {
        $statement = $this->databaseHandler->dbConnection->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
        $statement->execute([$name, $price]);

        return $this->databaseHandler->dbConnection->lastInsertId();
    }

    public function createSales(int $customerId, int $productId, int $productPrice, string $saleDate): void
    {
        $statement = $this->databaseHandler->dbConnection->prepare("INSERT INTO sales (customer_id, product_id, price, sale_date) VALUES (?, ?, ?, ?)");
        $statement->execute([$customerId, $productId, $productPrice, $saleDate]);
    }
}
