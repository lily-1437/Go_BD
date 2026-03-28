<?php
// public/login.php — LOGIN PAGE (Tourist + Admin)
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isLoggedIn())  { header("Location: ../tourist/dashboard.php"); exit(); }
if (isAdmin())     { header("Location: ../admin/dashboard.php");   exit(); }

$pageTitle = 'Login';
$error     = '';
$role      = $_POST['role'] ?? 'tourist';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($conn, $_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'] ?? 'tourist';

    if ($role === 'tourist') {
        $result = $conn->query(
            "SELECT p.id, p.name FROM PERSON p
             JOIN TOURIST t ON p.id = t.id
             WHERE p.email = '$email'"
        );
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // TODO: replace with password_verify($password, $hash) once passwords are hashed
            $_SESSION['tourist_id']   = $user['id'];
            $_SESSION['tourist_name'] = $user['name'];
            header("Location: ../tourist/dashboard.php"); exit();
        }
        $error = "Invalid email or password.";

    } elseif ($role === 'admin') {
        $result = $conn->query(
            "SELECT p.id, p.name FROM PERSON p
             JOIN ADMIN a ON p.id = a.id
             WHERE p.email = '$email'"
        );
        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header("Location: ../admin/dashboard.php"); exit();
        }
        $error = "Admin credentials not recognised.";
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="auth-layout">

    <!-- LEFT PANEL -->
    <div class="auth-panel auth-panel--visual">
        <div class="auth-panel__brand">🌿 Go BD</div>
        <div class="auth-panel__content">
            <h2>Welcome back,<br><em>Traveller</em></h2>
            <p>Log in to manage your bookings, plan trips, and explore the best of Bangladesh.</p>
            <ul class="auth-feature-list">
                <li>✅ Book hotels, guides &amp; transport</li>
                <li>✅ Plan personalised itineraries</li>
                <li>✅ Leave reviews &amp; get recommendations</li>
            </ul>
        </div>
        <div class="auth-panel__deco" aria-hidden="true">🏔️🌿🕌🌊</div>
    </div>

    <!-- RIGHT PANEL — FORM -->
    <div class="auth-panel auth-panel--form">
        <div class="auth-form-wrap">
            <h1>Sign In</h1>
            <p class="auth-subtitle">
                Don't have an account? <a href="register.php">Register free →</a>
            </p>

            <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- ROLE TOGGLE -->
            <div class="role-toggle">
                <button type="button"
                        class="role-btn <?= $role !== 'admin' ? 'active' : '' ?>"
                        data-role="tourist">
                    👤 Tourist
                </button>
                <button type="button"
                        class="role-btn <?= $role === 'admin' ? 'active' : '' ?>"
                        data-role="admin">
                    🛡️ Admin
                </button>
            </div>

            <form class="form" method="POST" id="loginForm">
                <input type="hidden" name="role" id="roleInput"
                       value="<?= htmlspecialchars($role) ?>">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required
                           placeholder="you@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password"
                               required placeholder="••••••••">
                        <button type="button" class="password-toggle" aria-label="Show password">👁</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full btn-lg">
                    Sign In
                </button>
            </form>

            <p class="auth-footer-note">
                By signing in you agree to our Terms of Service.
            </p>
        </div>
    </div>
</div>

<script>
// Role toggle
document.querySelectorAll('.role-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('roleInput').value = btn.dataset.role;
    });
});
// Show/hide password
document.querySelector('.password-toggle').addEventListener('click', function () {
    const inp = document.getElementById('password');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    this.textContent = inp.type === 'password' ? '👁' : '🙈';
});
</script>

<?php include '../includes/footer.php'; ?>
