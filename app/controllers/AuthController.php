<?php
class AuthController
{
    /** Public login (patients; staff/admin can also use it). */
    public static function login(): void
    {
        self::handleLogin('auth/login', 'auth/login', false);
    }

    /** Dedicated Staff & Admin portal: patients are rejected here. */
    public static function staffLogin(): void
    {
        self::handleLogin('auth/staff', 'auth/staff_login', true);
    }

    private static function handleLogin(string $route, string $viewName, bool $staffOnly): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $stmt = db()->prepare('SELECT user_id, username, password_hash, role FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $u = $stmt->fetch();
            if ($u && password_verify($password, $u['password_hash'])) {
                if ($staffOnly && !in_array($u['role'], ['admin', 'staff'], true)) {
                    flash('error', 'This portal is for staff and administrators only.');
                    redirect($route);
                }
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int)$u['user_id'], 'username' => $u['username'], 'role' => $u['role']];
                flash('success', 'Welcome back, ' . $u['username'] . '!');
                redirect('appointments/index');
            }
            flash('error', 'Invalid username or password.');
            redirect($route);
        }
        view($viewName === 'auth/login' ? 'auth/login' : $viewName);
    }

    public static function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $fullName = trim($_POST['full_name'] ?? '');
            $dob = trim($_POST['dob'] ?? '');
            $gender = $_POST['gender'] ?? '';
            $phone = trim($_POST['phone'] ?? '');
            $errors = [];
            if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) $errors[] = 'Username must be 3-30 letters, digits or underscores.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) $errors[] = 'Enter a valid email.';
            if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
            if ($fullName === '' || strlen($fullName) > 100) $errors[] = 'Enter your full name (max 100 characters).';
            $birth = DateTime::createFromFormat('Y-m-d', $dob);
            if (!$birth || $birth->format('Y-m-d') !== $dob || $birth > new DateTime('today')) $errors[] = 'Enter a valid date of birth.';
            if (!in_array($gender, ['Male', 'Female', 'Other'], true)) $errors[] = 'Choose a gender.';
            if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) $errors[] = 'Phone must contain 7-15 digits.';
            if (!$errors) {
                $stmt = db()->prepare('SELECT 1 FROM users WHERE username = ? OR email = ?');
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) $errors[] = 'Username or email already in use.';
            }
            if ($errors) {
                flash('error', implode(' ', $errors));
                redirect('auth/register');
            }
            // Self-registration is always a 'patient' (role cannot be chosen by the user).
            $pdo = db();
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, "patient")');
                $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                $userId = (int)$pdo->lastInsertId();
                $stmt = $pdo->prepare('INSERT INTO patients (user_id, full_name, dob, gender, phone) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$userId, $fullName, $dob, $gender, $phone]);
                $pdo->commit();
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }
            flash('success', 'Account created. Please log in.');
            redirect('auth/login');
        }
        view('auth/register');
    }

    public static function logout(): void
    {
        require_post();
        $_SESSION = [];
        session_destroy();
        session_start();
        flash('success', 'You have been logged out.');
        redirect('auth/login');
    }
}
