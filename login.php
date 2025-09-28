<?php
session_start();
require_once 'config.php';

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
            
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Неверный логин или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - АвтоНаЧас</title>
      <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php">АвтоНаЧас</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Главная</a></li>
                    <li><a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Вход</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                </ul>
            </nav>
        </div>
    </header>

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
                                <a href="index.php" class="auth-link auth-link-secondary">
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

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-main">
                    <div class="logo">АвтоНаЧас</div>
                    <p>Сервис аренды автомобилей с 2023 года</p>
                </div>
                <div class="footer-links">
                    <a href="#">О компании</a>
                    <a href="#">Контакты</a>
                    <a href="#">Помощь</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 АвтоНаЧас. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            if (usernameInput && !usernameInput.value) {
                usernameInput.focus();
            }
        });

     
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username) {
                e.preventDefault();
                alert('Пожалуйста, введите логин');
                return false;
            }
            
            if (!password) {
                e.preventDefault();
                alert('Пожалуйста, введите пароль');
                return false;
            }
        });
    </script>
</body>
</html>