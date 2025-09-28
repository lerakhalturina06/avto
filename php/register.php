<?php
session_start();
require_once '../config.php';
require_once 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
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
    $form_data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'username' => trim($_POST['username'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];

    $errors = validateRegistration($form_data);

    if (empty($errors)) {
        if (userExists($pdo, $form_data['username'], $form_data['email'])) {
            $errors[] = 'Пользователь с таким логином или email уже существует';
        }
    }
    
    if (empty($errors)) {
        if (createUser($pdo, $form_data)) {
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

$page_title = "Регистрация - АвтоНаЧас";
require_once 'header.php';
?>

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
                                <a href="../index.php" class="auth-link auth-link-secondary">
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

<?php require_once 'footer.php'; ?>