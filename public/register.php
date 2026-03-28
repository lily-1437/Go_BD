<?php
// public/register.php — TOURIST REGISTRATION PAGE
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) { header("Location: ../tourist/dashboard.php"); exit(); }

$pageTitle = 'Register';
$error     = '';
$success   = '';
$step      = (int)($_POST['step'] ?? 1);

// Keep field values on error
$vals = [
    'fname'          => $_POST['fname']          ?? '',
    'lname'          => $_POST['lname']          ?? '',
    'email'          => $_POST['email']          ?? '',
    'nationality'    => $_POST['nationality']    ?? '',
    'traveller_type' => $_POST['traveller_type'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $fname       = clean($conn, $vals['fname']);
    $lname       = clean($conn, $vals['lname']);
    $email       = clean($conn, $vals['email']);
    $nationality = clean($conn, $vals['nationality']);
    $trav_type   = clean($conn, $vals['traveller_type']);
    $password    = $_POST['password'] ?? '';
    $confirm     = $_POST['confirm_password'] ?? '';

    // Validation
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $check = $conn->query("SELECT id FROM PERSON WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $error = "An account with this email already exists. <a href='login.php'>Login instead?</a>";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $conn->begin_transaction();
            try {
                $conn->query(
                    "INSERT INTO PERSON (name, email) VALUES ('$fname $lname', '$email')"
                );
                $new_id = $conn->insert_id;
                $conn->query(
                    "INSERT INTO TOURIST (id, nationality, traveller_type)
                     VALUES ($new_id, '$nationality', '$trav_type')"
                );
                $conn->commit();
                $success = true;
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="auth-layout">

    <!-- LEFT PANEL -->
    <div class="auth-panel auth-panel--visual auth-panel--register">
        <div class="auth-panel__brand">🌿 Go BD</div>
        <div class="auth-panel__content">
            <h2>Start Your<br><em>Adventure</em></h2>
            <p>Join thousands of travellers exploring the best of Bangladesh.</p>

            <div class="traveller-types">
                <div class="ttype-card ttype--family">
                    <span class="ttype-icon">👨‍👩‍👧</span>
                    <span>Family</span>
                </div>
                <div class="ttype-card ttype--solo">
                    <span class="ttype-icon">🧭</span>
                    <span>Solo</span>
                </div>
                <div class="ttype-card ttype--female">
                    <span class="ttype-icon">🌸</span>
                    <span>Female Solo</span>
                </div>
            </div>

            <ul class="auth-feature-list">
                <li>✅ Personalised trip recommendations</li>
                <li>✅ Safety info for solo travellers</li>
                <li>✅ Book in minutes, travel hassle-free</li>
                <li>✅ 100% free — no hidden charges</li>
            </ul>
        </div>
        <div class="auth-panel__deco" aria-hidden="true">🏔️🌿🕌🌊</div>
    </div>

    <!-- RIGHT PANEL — FORM -->
    <div class="auth-panel auth-panel--form">
        <div class="auth-form-wrap">

            <?php if ($success): ?>
            <!-- SUCCESS STATE -->
            <div class="register-success">
                <div class="register-success__icon">🎉</div>
                <h2>You're all set!</h2>
                <p>Welcome to Go BD, <strong><?= htmlspecialchars($vals['fname']) ?></strong>!<br>
                   Your account has been created successfully.</p>
                <a href="login.php" class="btn btn-primary btn-lg btn-full" style="margin-top:1.5rem">
                    Login to your account →
                </a>
                <a href="places.php" class="btn btn-ghost btn-full" style="margin-top:.5rem">
                    Explore places first
                </a>
            </div>

            <?php else: ?>
            <!-- REGISTRATION FORM -->
            <h1>Create Account</h1>
            <p class="auth-subtitle">
                Already registered? <a href="login.php">Sign in →</a>
            </p>

            <!-- PROGRESS -->
            <div class="reg-progress">
                <div class="reg-progress__step <?= $step >= 1 ? 'done' : '' ?>">
                    <span>1</span> Personal Info
                </div>
                <div class="reg-progress__line"></div>
                <div class="reg-progress__step <?= $step >= 2 ? 'done' : '' ?>">
                    <span>2</span> Password
                </div>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form class="form" method="POST" id="regForm">
                <input type="hidden" name="step" id="stepInput" value="<?= $step ?>">

                <!-- ═══ STEP 1 ═══ -->
                <div id="step1" class="reg-step <?= $step > 1 && !$error ? 'hidden' : '' ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fname">First Name *</label>
                            <input type="text" id="fname" name="fname" required
                                   placeholder="e.g. Rahim"
                                   value="<?= htmlspecialchars($vals['fname']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="lname">Last Name *</label>
                            <input type="text" id="lname" name="lname" required
                                   placeholder="e.g. Uddin"
                                   value="<?= htmlspecialchars($vals['lname']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               placeholder="you@example.com"
                               value="<?= htmlspecialchars($vals['email']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="nationality">Nationality *</label>
                        <input type="text" id="nationality" name="nationality" required
                               placeholder="e.g. Bangladeshi"
                               value="<?= htmlspecialchars($vals['nationality']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Traveller Type *</label>
                        <span class="form-hint">This helps us give you better recommendations.</span>
                        <div class="ttype-select">
                            <label class="ttype-option">
                                <input type="radio" name="traveller_type" value="family"
                                       <?= $vals['traveller_type'] === 'family' ? 'checked' : '' ?> required>
                                <span class="ttype-option__card">
                                    <span class="ttype-option__icon">👨‍👩‍👧</span>
                                    <span class="ttype-option__label">Family</span>
                                    <span class="ttype-option__desc">Travelling with family</span>
                                </span>
                            </label>
                            <label class="ttype-option">
                                <input type="radio" name="traveller_type" value="solo"
                                       <?= $vals['traveller_type'] === 'solo' ? 'checked' : '' ?>>
                                <span class="ttype-option__card">
                                    <span class="ttype-option__icon">🧭</span>
                                    <span class="ttype-option__label">Solo</span>
                                    <span class="ttype-option__desc">Independent traveller</span>
                                </span>
                            </label>
                            <label class="ttype-option">
                                <input type="radio" name="traveller_type" value="female_solo"
                                       <?= $vals['traveller_type'] === 'female_solo' ? 'checked' : '' ?>>
                                <span class="ttype-option__card">
                                    <span class="ttype-option__icon">🌸</span>
                                    <span class="ttype-option__label">Female Solo</span>
                                    <span class="ttype-option__desc">Solo female traveller</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-full btn-lg" id="nextBtn">
                        Continue →
                    </button>
                </div>

                <!-- ═══ STEP 2 ═══ -->
                <div id="step2" class="reg-step <?= $step < 2 && !$error ? 'hidden' : ($step < 2 ? 'hidden' : '') ?>">

                    <!-- Keep step 1 values -->
                    <input type="hidden" name="fname"          value="<?= htmlspecialchars($vals['fname']) ?>">
                    <input type="hidden" name="lname"          value="<?= htmlspecialchars($vals['lname']) ?>">
                    <input type="hidden" name="email"          value="<?= htmlspecialchars($vals['email']) ?>">
                    <input type="hidden" name="nationality"    value="<?= htmlspecialchars($vals['nationality']) ?>">
                    <input type="hidden" name="traveller_type" value="<?= htmlspecialchars($vals['traveller_type']) ?>">

                    <div class="step2-greeting">
                        <p>Almost done, <strong><?= htmlspecialchars($vals['fname'] ?: 'Traveller') ?></strong>! 🎒</p>
                        <p class="text-sm text-muted">Just set a password to secure your account.</p>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password"
                                   required placeholder="At least 8 characters" minlength="8">
                            <button type="button" class="password-toggle" aria-label="Show">👁</button>
                        </div>
                        <div class="password-strength" id="strengthBar">
                            <div class="strength-track"><div class="strength-fill" id="strengthFill"></div></div>
                            <span id="strengthLabel" class="text-xs"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <div class="password-wrap">
                            <input type="password" id="confirm_password" name="confirm_password"
                                   required placeholder="Repeat your password">
                            <button type="button" class="password-toggle" data-target="confirm_password" aria-label="Show">👁</button>
                        </div>
                        <span class="form-hint" id="matchHint"></span>
                    </div>

                    <div class="step2-summary">
                        <p>📧 <?= htmlspecialchars($vals['email']) ?></p>
                        <p>🌍 <?= htmlspecialchars($vals['nationality']) ?></p>
                        <p>🧳 <?= htmlspecialchars(str_replace('_', ' ', ucfirst($vals['traveller_type']))) ?></p>
                    </div>

                    <button type="submit" class="btn btn-accent btn-full btn-lg">
                        🎉 Create My Account
                    </button>

                    <button type="button" class="btn btn-ghost btn-sm" id="backBtn" style="margin-top:.5rem;width:100%">
                        ← Go Back
                    </button>
                </div>

            </form>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
// Multi-step navigation
const step1El  = document.getElementById('step1');
const step2El  = document.getElementById('step2');
const stepInput = document.getElementById('stepInput');

document.getElementById('nextBtn')?.addEventListener('click', () => {
    // Basic step 1 validation
    const required = step1El.querySelectorAll('[required]');
    let valid = true;
    required.forEach(el => { if (!el.value.trim()) { el.focus(); valid = false; } });
    const ttype = document.querySelector('[name="traveller_type"]:checked');
    if (!ttype) { alert('Please select your traveller type.'); valid = false; }
    if (!valid) return;

    step1El.classList.add('hidden');
    step2El.classList.remove('hidden');
    stepInput.value = 2;
});

document.getElementById('backBtn')?.addEventListener('click', () => {
    step2El.classList.add('hidden');
    step1El.classList.remove('hidden');
    stepInput.value = 1;
});

// Password toggle
document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', function () {
        const target = this.dataset.target || 'password';
        const inp = document.getElementById(target);
        inp.type = inp.type === 'password' ? 'text' : 'password';
        this.textContent = inp.type === 'password' ? '👁' : '🙈';
    });
});

// Password strength
const pwInput = document.getElementById('password');
const fill    = document.getElementById('strengthFill');
const label   = document.getElementById('strengthLabel');

if (pwInput) {
    pwInput.addEventListener('input', () => {
        const v = pwInput.value;
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        const pct   = ['0%','25%','50%','75%','100%'][score];
        const color = ['#ccc','#e74c3c','#e67e22','#f1c40f','#2ecc71'][score];
        const lbl   = ['','Weak','Fair','Good','Strong'][score];
        fill.style.width = pct; fill.style.background = color; label.textContent = lbl;
    });
}

// Match hint
const confirm = document.getElementById('confirm_password');
const hint    = document.getElementById('matchHint');
if (confirm) {
    confirm.addEventListener('input', () => {
        if (!confirm.value) { hint.textContent = ''; return; }
        const match = confirm.value === pwInput.value;
        hint.textContent = match ? '✅ Passwords match' : '❌ Passwords do not match';
        hint.style.color = match ? '#2d6b29' : '#c0392b';
    });
}
</script>

<?php include '../includes/footer.php'; ?>
