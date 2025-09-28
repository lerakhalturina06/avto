<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Определяем базовый путь в зависимости от местоположения
if (strpos($_SERVER['PHP_SELF'], 'php/') !== false) {
    $base_path = '../';
} else {
    $base_path = '';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'АвтоНаЧас'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>style/style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="<?php echo $base_path; ?>index.php">АвтоНаЧас</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Главная</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_path; ?>php/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user"></i> Кабинет</a></li>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li><a href="<?php echo $base_path; ?>php/admin.php" class="<?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">
                                <i class="fas fa-cog"></i> Админ</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_path; ?>php/logout.php"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_path; ?>php/login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">
                            <i class="fas fa-sign-in-alt"></i> Вход</a></li>
                        <li><a href="<?php echo $base_path; ?>php/register.php" class="btn-register <?php echo $current_page == 'register.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>