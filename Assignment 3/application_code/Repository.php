<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

final class Repository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->pdo();
    }

    // Create a new profile and return its new ID.
    public function createProfile(
        string $firstName,
        string $lastName,
        string $email,
        ?string $bio,
        string $imagePath,
        ?string $imageMime = null,
        ?int $imageSize = null
    ): int {
        $sql = "
            INSERT INTO profiles
              (first_name, last_name, email, bio, image_path, image_mime, image_size_bytes)
            VALUES
              (:first_name, :last_name, :email, :bio, :image_path, :image_mime, :image_size_bytes)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first_name'       => $firstName,
            ':last_name'        => $lastName,
            ':email'            => $email,
            ':bio'              => $bio,
            ':image_path'       => $imagePath,
            ':image_mime'       => $imageMime,
            ':image_size_bytes' => $imageSize,
        ]);

        return (int)$this->db->lastInsertId();
    }

    // Fetch all profiles, most recent first.
    public function fetchAllProfiles(): array
    {
        $sql = "SELECT id, first_name, last_name, email, bio, image_path, created_at
                  FROM profiles
              ORDER BY created_at DESC, id DESC";

        return $this->db->query($sql)->fetchAll();
    }
}
