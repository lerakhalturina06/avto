<?php
session_start();
require_once 'config.php';

$brand = $_GET['brand'] ?? '';
$type = $_GET['type'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

$sql = "SELECT * FROM cars WHERE available = TRUE";
$params = [];

if (!empty($brand)) {
    $sql .= " AND brand LIKE ?";
    $params[] = "%$brand%";
}

if (!empty($type)) {
    $sql .= " AND type = ?";
    $params[] = $type;
}

if (!empty($min_price)) {
    $sql .= " AND price_per_day >= ?";
    $params[] = $min_price;
}

if (!empty($max_price)) {
    $sql .= " AND price_per_day <= ?";
    $params[] = $max_price;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

$brands = $pdo->query("SELECT DISTINCT brand FROM cars ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
$types = $pdo->query("SELECT DISTINCT type FROM cars ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аренда автомобилей - АвтоНаЧас</title>
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
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Главная</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php"><i class="fas fa-user"></i> Кабинет</a></li>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li><a href="admin.php"><i class="fas fa-cog"></i> Админ</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Вход</a></li>
                        <li><a href="register.php" class="btn-register"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Аренда премиальных автомобилей</h1>
                    <p>Быстро. Удобно. Выгодно. Выберите идеальный автомобиль для вашей поездки</p>
                    
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="hero-actions">
                            <a href="register.php" class="btn btn-primary">
                                <i class="fas fa-rocket"></i>
                                Начать аренду
                            </a>
                            <a href="#cars" class="btn btn-secondary">
                                <i class="fas fa-search"></i>
                                Смотреть авто
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="hero-actions">
                            <a href="#cars" class="btn btn-primary">
                                <i class="fas fa-car"></i>
                                Выбрать авто
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">автомобилей</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">поддержка</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">страховка</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">5★</div>
                        <div class="stat-label">рейтинг</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="filters-section">
            <div class="container">
                <div class="section-header">
                    <h2>Найдите свой автомобиль</h2>
                    <p>Отфильтруйте по параметрам</p>
                </div>
                <form method="GET" class="filter-form">
                    <div class="filter-grid">
                        <div class="form-group">
                            <select id="brand" name="brand">
                                <option value="">Все марки</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= htmlspecialchars($b) ?>" <?= $brand == $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <select id="type" name="type">
                                <option value="">Все типы</option>
                                <?php foreach ($types as $t): ?>
                                    <option value="<?= htmlspecialchars($t) ?>" <?= $type == $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <input type="number" id="min_price" name="min_price" value="<?= htmlspecialchars($min_price) ?>" placeholder="Цена от" min="0">
                        </div>
                        
                        <div class="form-group">
                            <input type="number" id="max_price" name="max_price" value="<?= htmlspecialchars($max_price) ?>" placeholder="Цена до" min="0">
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary btn-small">
                            <i class="fas fa-filter"></i>
                            Применить
                        </button>
                        <a href="index.php" class="btn btn-secondary btn-small">
                            <i class="fas fa-times"></i>
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <section class="cars-section" id="cars">
            <div class="container">
                <div class="section-header">
                    <h2>Доступные автомобили</h2>
                    <p>Выберите подходящий вариант</p>
                </div>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="quick-register">
                        <span>Хотите арендовать?</span>
                        <a href="register.php" class="btn-link">Зарегистрируйтесь →</a>
                    </div>
                <?php endif; ?>
                
                <div class="cars-grid">
                    <?php if (count($cars) > 0): ?>
                        <?php foreach ($cars as $car): ?>
                            <div class="car-card">
                                <div class="car-image">
                                    <?php if (file_exists('images/' . $car['image_url']) && !empty($car['image_url'])): ?>
                                        <img src="images/<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?>">
                                    <?php else: ?>
                                        <div class="car-image-placeholder">
                                            <i class="fas fa-car"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="car-badge">В наличии</div>
                                </div>
                                
                                <div class="car-content">
                                    <div class="car-header">
                                        <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                                        <div class="car-year"><?= htmlspecialchars($car['year']) ?></div>
                                    </div>
                                    
                                    <div class="car-meta">
                                        <span class="car-type"><?= htmlspecialchars($car['type']) ?></span>
                                        <span class="car-dot">•</span>
                                        <span class="car-year"><?= htmlspecialchars($car['year']) ?> г.</span>
                                    </div>
                                    
                                    <p class="car-description"><?= htmlspecialchars(mb_substr($car['description'], 0, 80)) ?>...</p>
                                    
                                    <div class="car-footer">
                                        <div class="car-price">
                                            <span class="price"><?= number_format($car['price_per_day'], 0, ',', ' ') ?> ₽</span>
                                            <span class="price-label">/час</span>
                                        </div>
                                        
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <a href="rent.php?car_id=<?= $car['id'] ?>" class="btn btn-primary btn-small">
                                                <i class="fas fa-calendar"></i>
                                                Арендовать
                                            </a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-secondary btn-small">
                                                <i class="fas fa-lock"></i>
                                                Войти
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-cars">
                            <i class="fas fa-search"></i>
                            <h3>Ничего не найдено</h3>
                            <p>Попробуйте другие параметры поиска</p>
                            <a href="index.php" class="btn btn-primary btn-small">Сбросить фильтры</a>
                        </div>
                    <?php endif; ?>
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
</body>
</html>