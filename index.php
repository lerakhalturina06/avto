<?php
session_start();
require_once 'config.php';
require_once 'php/functions.php';

$filters = [
    'brand' => $_GET['brand'] ?? '',
    'type' => $_GET['type'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? ''
];

try {
    $cars = getCars($pdo, $filters);
    $brands = getBrands($pdo);
    $types = getTypes($pdo);
} catch (Exception $e) {
    $cars = [];
    $brands = [];
    $types = [];
    $error_message = "Ошибка при загрузке данных: " . $e->getMessage();
}

$page_title = "Аренда автомобилей - АвтоНаЧас";
require_once 'php/header.php';
?>

<main>
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Аренда премиальных автомобилей</h1>
                <p>Быстро. Удобно. Выгодно. Выберите идеальный автомобиль для вашей поездки</p>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="hero-actions">
                        <a href="php/register.php" class="btn btn-primary">
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
                                <option value="<?= htmlspecialchars($b) ?>" <?= $filters['brand'] == $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <select id="type" name="type">
                            <option value="">Все типы</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>" <?= $filters['type'] == $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="number" id="min_price" name="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>" placeholder="Цена от" min="0">
                    </div>
                    
                    <div class="form-group">
                        <input type="number" id="max_price" name="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>" placeholder="Цена до" min="0">
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
                    <a href="php/register.php" class="btn-link">Зарегистрируйтесь →</a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <p><?= htmlspecialchars($error_message) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="cars-grid">
                <?php if (!empty($cars) && count($cars) > 0): ?>
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
                                        <a href="php/rent.php?car_id=<?= $car['id'] ?>" class="btn btn-primary btn-small">
                                            <i class="fas fa-calendar"></i>
                                            Арендовать
                                        </a>
                                    <?php else: ?>
                                        <a href="php/login.php" class="btn btn-secondary btn-small">
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

<?php require_once 'php/footer.php'; ?>