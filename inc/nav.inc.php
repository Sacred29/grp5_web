<nav class="navbar navbar-expand-md">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/products.php">Products</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cart.php">Shopping Cart</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/about.php">About us</a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['email'])) : ?>
                <li class="nav-item d-flex align-items-center">
                    <span class="navbar-text mr-3">
                        <?php if ($_SESSION['user_privilege'] == 'admin') : ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Admin</a>
                </li>
            <?php elseif ($_SESSION['user_privilege'] == 'staff') : ?>
                <li class="nav-item">
                    <a class="nav-link" href="/staff.php">Staff</a>
                </li>
            <?php endif ?>
            </span>
            <li class="nav-item d-flex align-items-center">
                <span class="nav-link">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['fName']) . " " . htmlspecialchars($_SESSION['lName']); ?>
                </span>
            </li>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/account.php">Account</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout.php">Logout</a>
            </li>
        <?php else : ?>
            <li class="nav-item">
                <a class="nav-link" href="/register/register.php">Register</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/login/login.php">Login</a>
            </li>
        <?php endif; ?>
        </ul>
    </div>
</nav>