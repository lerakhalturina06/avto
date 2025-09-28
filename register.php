<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];
$success = '';

$form_data = [
    'full_name' => '',
    'phone' => '',
    'email' => '',
    'username' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['full_name'] = trim($_POST['full_name'] ?? '');
    $form_data['phone'] = trim($_POST['phone'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $form_data['username'] = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($form_data['full_name'])) {
        $errors[] = 'ФИО обязательно для заполнения';
    }
    
    if (empty($form_data['phone']) || !preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $form_data['phone'])) {
        $errors[] = 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX';
    }
    
    if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }
    
    if (empty($form_data['username'])) {
        $errors[] = 'Логин обязателен для заполнения';
    } elseif (strlen($form_data['username']) < 3) {
        $errors[] = 'Логин должен содержать минимум 3 символа';
    }
    
    if (empty($password)) {
        $errors[] = 'Пароль обязателен для заполнения';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$form_data['username'], $form_data['email']]);
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким логином или email уже существует';
        }
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, email, username, password) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$form_data['full_name'], $form_data['phone'], $form_data['email'], $form_data['username'], $hashed_password])) {
            $success = 'Регистрация прошла успешно! Через 3 секунды вы будете перенаправлены на страницу входа.';
            $form_data = [
                'full_name' => '',
                'phone' => '',
                'email' => '',
                'username' => ''
            ];
            header('Refresh: 3; url=login.php');
        } else {
            $errors[] = 'Ошибка при регистрации. Попробуйте позже.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - АвтоНаЧас</title>
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
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Вход</a></li>
                    <li><a href="register.php" class="active"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="auth-section">
            <div class="container">
                <div class="section-header">
                    <h2>Создание аккаунта</h2>
                    <p>Присоединяйтесь к сервису аренды автомобилей</p>
                </div>
                
                <div class="auth-container">
                    <div class="auth-card">
                        <?php if (!empty($errors)): ?>
                            <div class="error-messages">
                                <div class="error-header">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>Ошибки при заполнении формы:</span>
                                </div>
                                <?php foreach ($errors as $error): ?>
                                    <div class="error-item"><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="success-message">
                                <div class="success-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h3>Регистрация успешна!</h3>
                                <p><?= htmlspecialchars($success) ?></p>
                                <div class="success-actions">
                                    <a href="login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Перейти к входу
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <form method="POST" id="registrationForm" class="auth-form">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="full_name" class="form-label">
                                            <i class="fas fa-user"></i>
                                            ФИО
                                        </label>
                                        <input type="text" id="full_name" name="full_name" 
                                               value="<?= htmlspecialchars($form_data['full_name']) ?>" 
                                               required placeholder="Введите ваше полное имя"
                                               class="form-input">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone"></i>
                                            Телефон
                                        </label>
                                        <input type="tel" id="phone" name="phone" 
                                               value="<?= htmlspecialchars($form_data['phone']) ?>" 
                                               required placeholder="+7(XXX)-XXX-XX-XX"
                                               class="form-input">
                                        <div class="form-hint">Формат: +7(XXX)-XXX-XX-XX</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i>
                                            Email
                                        </label>
                                        <input type="email" id="email" name="email" 
                                               value="<?= htmlspecialchars($form_data['email']) ?>" 
                                               required placeholder="example@mail.ru"
                                               class="form-input">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-user-circle"></i>
                                            Логин
                                        </label>
                                        <input type="text" id="username" name="username" 
                                               value="<?= htmlspecialchars($form_data['username']) ?>" 
                                               required placeholder="Придумайте логин"
                                               class="form-input">
                                        <div class="form-hint">Минимум 3 символа</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock"></i>
                                            Пароль
                                        </label>
                                        <input type="password" id="password" name="password" 
                                               required placeholder="Введите пароль"
                                               class="form-input">
                                        <div class="form-hint">Минимум 6 символов</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">
                                            <i class="fas fa-lock"></i>
                                            Подтверждение пароля
                                        </label>
                                        <input type="password" id="confirm_password" name="confirm_password" 
                                               required placeholder="Повторите пароль"
                                               class="form-input">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-full">
                                    <i class="fas fa-user-plus"></i>
                                    Создать аккаунт
                                </button>
                            </form>
                            
                            <div class="auth-links">
                                <div class="auth-link-item">
                                    <span>Уже есть аккаунт?</span>
                                    <a href="login.php" class="auth-link">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Войти в систему
                                    </a>
                                </div>
                                <div class="auth-link-item">
                                    <a href="index.php" class="auth-link auth-link-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Вернуться на главную
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
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
        document.getElementById('phone').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (x) {
                e.target.value = '+7(' + (x[2] ? x[2] : '') + (x[3] ? ')-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            }
        });

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Пароли не совпадают!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Пароль должен содержать минимум 6 символов!');
                return false;
            }
        });
    </script>
</body>
</html>