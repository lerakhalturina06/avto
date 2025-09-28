<?php
function getCars($pdo, $filters = []) {
    $sql = "SELECT * FROM cars WHERE available = TRUE";
    $params = [];
    
    if (!empty($filters['brand'])) {
        $sql .= " AND brand LIKE ?";
        $params[] = "%{$filters['brand']}%";
    }
    
    if (!empty($filters['type'])) {
        $sql .= " AND type = ?";
        $params[] = $filters['type'];
    }
    
    if (!empty($filters['min_price'])) {
        $sql .= " AND price_per_day >= ?";
        $params[] = $filters['min_price'];
    }
    
    if (!empty($filters['max_price'])) {
        $sql .= " AND price_per_day <= ?";
        $params[] = $filters['max_price'];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBrands($pdo) {
    return $pdo->query("SELECT DISTINCT brand FROM cars ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
}

function getTypes($pdo) {
    return $pdo->query("SELECT DISTINCT type FROM cars ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
}

function validateRegistration($data) {
    $errors = [];
    
    if (empty($data['full_name'])) {
        $errors[] = 'ФИО обязательно для заполнения';
    }
    
    if (empty($data['phone']) || !preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $data['phone'])) {
        $errors[] = 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }
    
    if (empty($data['username']) || strlen($data['username']) < 3) {
        $errors[] = 'Логин должен содержать минимум 3 символа';
    }
    
    if (empty($data['password']) || strlen($data['password']) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов';
    }
    
    if ($data['password'] !== $data['confirm_password']) {
        $errors[] = 'Пароли не совпадают';
    }
    
    return $errors;
}

function userExists($pdo, $username, $email) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    return $stmt->fetch() !== false;
}

function createUser($pdo, $userData) {
    $hashed_password = password_hash($userData['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, email, username, password) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([
        $userData['full_name'], 
        $userData['phone'], 
        $userData['email'], 
        $userData['username'], 
        $hashed_password
    ]);
}
?>