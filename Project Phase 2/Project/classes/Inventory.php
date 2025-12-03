<?php

class Inventory
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Create admin user
    public function createAdmin(string $name, string $email, string $password, string $confirm): array
    {
        $name  = trim($name);
        $email = trim($email);

        if ($name === '') {
            return ['ok' => false, 'error' => 'Name is required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Valid email is required.'];
        }

        if ($password === '' || strlen($password) < 6) {
            return ['ok' => false, 'error' => 'Password must be at least 6 characters.'];
        }

        if ($password !== $confirm) {
            return ['ok' => false, 'error' => 'Passwords do not match.'];
        }

        // Check duplicate email
        $check = $this->pdo->prepare('SELECT COUNT(*) FROM admins WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            return ['ok' => false, 'error' => 'Email is already registered.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO admins (name, email, password) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $email, $hash]);

        return ['ok' => true];
    }

    // Get admin by email for login
    public function getAdminByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }


    // Create product
    // $price is in dollars; stored in DB as cents
    public function createProduct(
    string $name,
    string $description,
    float $price,
    int $quantity,
    string $imageName
): array {
    $name        = trim($name);
    $description = trim($description);
    $imageName   = trim($imageName);

    // --- validation ---
    if ($name === '') {
        return ['ok' => false, 'error' => 'Name is required.'];
    }

    if ($description === '') {
        return ['ok' => false, 'error' => 'Description is required.'];
    }

    if ($price <= 0) {
        return ['ok' => false, 'error' => 'Price must be greater than zero.'];
    }

    if ($quantity < 0) {
        return ['ok' => false, 'error' => 'Quantity cannot be negative.'];
    }

    if ($imageName === '') {
        return ['ok' => false, 'error' => 'Image file name is required.'];
    }

    // convert dollars to cents
    $priceCents = (int) round($price * 100);

    // IMPORTANT: foreign key – use an existing category id
    // assuming id=1 exists in your categories table
    $categoryId = 1;

    $sql = "INSERT INTO products
              (category_id, name, short_desc, price_cents, quantity, image, is_active, created_at)
            VALUES
              (:category_id, :name, :short_desc, :price_cents, :quantity, :image, 1, NOW())";

    $stmt = $this->pdo->prepare($sql);

    try {
        $stmt->execute([
            ':category_id' => $categoryId,
            ':name'        => $name,
            ':short_desc'  => $description,
            ':price_cents' => $priceCents,
            ':quantity'    => $quantity,
            ':image'       => $imageName,
        ]);
    } catch (PDOException $e) {
        return ['ok' => false, 'error' => 'DB error: ' . $e->getMessage()];
    }

    return ['ok' => true];
}

    // Read: all products (public view)
    public function getAllProducts(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name, short_desc, price_cents, quantity, image
             FROM products
             WHERE is_active = 1
             ORDER BY id DESC'
        );
        return $stmt->fetchAll();
    }

    // Read: single product by id
    public function getProductById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, short_desc, price_cents, quantity, image
             FROM products
             WHERE id = ? AND is_active = 1
             LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Update product
    public function updateProduct(
        int $id,
        string $name,
        string $description,
        $price,
        $quantity,
        ?string $imageName = null
    ): array {
        $name        = trim($name);
        $description = trim($description);

        if ($name === '' || $description === '') {
            return ['ok' => false, 'error' => 'Name and description are required.'];
        }

        if (!is_numeric($price) || $price < 0) {
            return ['ok' => false, 'error' => 'Price must be a non-negative number.'];
        }

        if (!is_numeric($quantity) || $quantity < 0 || floor($quantity) != $quantity) {
            return ['ok' => false, 'error' => 'Quantity must be a non-negative integer.'];
        }

        $priceCents = (int) round($price * 100);

        if ($imageName !== null && $imageName !== '') {
            $sql = 'UPDATE products
                    SET name = ?, short_desc = ?, price_cents = ?, quantity = ?, image = ?
                    WHERE id = ?';
            $params = [$name, $description, $priceCents, (int)$quantity, $imageName, $id];
        } else {
            $sql = 'UPDATE products
                    SET name = ?, short_desc = ?, price_cents = ?, quantity = ?
                    WHERE id = ?';
            $params = [$name, $description, $priceCents, (int)$quantity, $id];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return ['ok' => true];
    }

    // Delete product
    public function deleteProduct(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }
}
