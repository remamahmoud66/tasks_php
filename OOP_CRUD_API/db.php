<?php
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'user_system');
define('DB_PORT', '3306');

class DB_con
{
    private PDO $connect;

    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $this->connect = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->connect;
    }

    public function fetchUsers(): array
    {
        return $this->connect->query('SELECT id, name, email FROM users ORDER BY id DESC')->fetchAll();
    }

    public function fetchUser(int $id): array|false
    {
        $stmt = $this->connect->prepare('SELECT id, name, email FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createUser(string $name, string $email, string $password): bool
    {
        $stmt = $this->connect->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function updateUser(int $id, string $name, string $email, ?string $password = null): bool
    {
        if ($password !== null && $password !== '') {
            $stmt = $this->connect->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?');
            return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $id]);
        }

        $stmt = $this->connect->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        return $stmt->execute([$name, $email, $id]);
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->connect->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function fetchCategories(): array
    {
        return $this->connect->query('SELECT id, name, description FROM categories ORDER BY id DESC')->fetchAll();
    }

    public function fetchCategory(int $id): array|false
    {
        $stmt = $this->connect->prepare('SELECT id, name, description FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createCategory(string $name, ?string $description): bool
    {
        $stmt = $this->connect->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
        return $stmt->execute([$name, $description]);
    }

    public function updateCategory(int $id, string $name, ?string $description): bool
    {
        $stmt = $this->connect->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
        return $stmt->execute([$name, $description, $id]);
    }

    public function deleteCategory(int $id): bool
    {
        $stmt = $this->connect->prepare('DELETE FROM categories WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function fetchProducts(): array
    {
        $sql = 'SELECT products.id, products.name, products.description, products.price,
                       products.quantity, products.category_id, products.image,
                       categories.name AS category_name
                FROM products
                LEFT JOIN categories ON products.category_id = categories.id
                ORDER BY products.id DESC';
        return $this->connect->query($sql)->fetchAll();
    }

    public function fetchProduct(int $id): array|false
    {
        $stmt = $this->connect->prepare(
            'SELECT id, name, description, price, quantity, category_id, image FROM products WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createProduct(
        string $name,
        ?string $description,
        float $price,
        int $quantity,
        int $categoryId,
        ?string $image
    ): bool {
        $stmt = $this->connect->prepare(
            'INSERT INTO products (name, description, price, quantity, category_id, image) VALUES (?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([$name, $description, $price, $quantity, $categoryId, $image]);
    }

    public function updateProduct(
        int $id,
        string $name,
        ?string $description,
        float $price,
        int $quantity,
        int $categoryId,
        ?string $image
    ): bool {
        $stmt = $this->connect->prepare(
            'UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, category_id = ?, image = ? WHERE id = ?'
        );
        return $stmt->execute([$name, $description, $price, $quantity, $categoryId, $image, $id]);
    }

    public function deleteProduct(int $id): bool
    {
        $stmt = $this->connect->prepare('DELETE FROM products WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
