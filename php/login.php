<?php
session_start();
require_once '../config.php';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $errors[] = 'Заполните все поля';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            header('Location: ../index.php');
            exit();
        } else {
            $errors[] = 'Неверный логин или пароль';
        }
    }
}

$page_title = "Вход - АвтоНаЧас";
require_once 'header.php';
?>

<main>
    <section class="auth-section">
        <div class="container">
            <div class="section-header">
                <h2>Вход в систему</h2>
                <p>Войдите в свой аккаунт для аренды автомобилей</p>
            </div>
            
            <div class="auth-container">
                <div class="auth-card">
                    <?php if (!empty($errors)): ?>
                        <div class="error-messages">
                            <div class="error-header">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>Ошибки при входе:</span>
                            </div>
                            <?php foreach ($errors as $error): ?>
                                <div class="error-item"><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="auth-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user-circle"></i>
                                    Логин
                                </label>
                                <input type="text" id="username" name="username" 
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                                       required placeholder="Введите ваш логин"
                                       class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Пароль
                                </label>
                                <input type="password" id="password" name="password" 
                                       required placeholder="Введите ваш пароль"
                                       class="form-input">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-sign-in-alt"></i>
                            Войти в систему
                        </button>
                    </form>
                    
                    <div class="auth-links">
                        <div class="auth-link-item">
                            <span>Еще нет аккаунта?</span>
                            <a href="register.php" class="auth-link">
                                <i class="fas fa-user-plus"></i>
                                Зарегистрироваться
                            </a>
                        </div>
                        <div class="auth-link-item">
                            <a href="../index.php" class="auth-link auth-link-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Вернуться на главную
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once '../templates/footer.php'; ?>