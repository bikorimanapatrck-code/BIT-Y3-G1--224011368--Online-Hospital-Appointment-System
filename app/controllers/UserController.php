<?php
class UserController
{
    private static function find(int $id): ?array
    {
        $s = db()->prepare('SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    /**
     * Who may edit/delete whom (managing OTHER accounts; own account uses "My profile"):
     *  - admin : any other account
     *  - staff : patient accounts only
     */
    public static function canManage(array $target): bool
    {
        $me = current_user();
        if (!$me || (int)$target['user_id'] === $me['id']) return false;
        if ($me['role'] === 'admin') return true;
        return $me['role'] === 'staff' && $target['role'] === 'patient';
    }

    public static function index(): void
    {
        require_role(['admin', 'staff']);
        $rows = db()->query('SELECT user_id, username, email, role, created_at FROM users ORDER BY user_id ASC')->fetchAll();
        view('users/index', compact('rows'));
    }

    public static function form(): void
    {
        require_role(['admin', 'staff']);
        $u = self::find((int)($_GET['id'] ?? 0));
        if (!$u) { http_response_code(404); exit('User not found.'); }
        if (!self::canManage($u)) { http_response_code(403); exit('You cannot edit this account.'); }
        $profile = false;
        view('users/form', ['target' => $u, 'profile' => $profile]);
    }

    public static function save(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $id = (int)($_POST['user_id'] ?? 0);
        $u = self::find($id);
        if (!$u) { http_response_code(404); exit('User not found.'); }
        if (!self::canManage($u)) { http_response_code(403); exit('You cannot edit this account.'); }

        [$err, $username, $email, $newPass] = self::collect($id);
        $role = $u['role'];
        if (current_user()['role'] === 'admin') {            // only admins may change roles
            $role = $_POST['role'] ?? $u['role'];
            if (!in_array($role, ['admin', 'staff', 'patient'], true)) $err[] = 'Invalid role.';
        }
        if ($err) {
            flash('error', implode(' ', $err));
            redirect("users/form&id=$id");
        }
        if ($newPass !== '') {
            $s = db()->prepare('UPDATE users SET username=?, email=?, role=?, password_hash=? WHERE user_id=?');
            $s->execute([$username, $email, $role, password_hash($newPass, PASSWORD_DEFAULT), $id]);
        } else {
            $s = db()->prepare('UPDATE users SET username=?, email=?, role=? WHERE user_id=?');
            $s->execute([$username, $email, $role, $id]);
        }
        flash('success', 'User updated.');
        redirect('users/index');
    }

    public static function role(): void
    {
        require_role(['admin']);
        require_post();
        $id = (int)($_POST['id'] ?? 0);
        $role = $_POST['role'] ?? '';
        if (!in_array($role, ['admin', 'staff', 'patient'], true) || $id === current_user()['id']) {
            flash('error', 'Invalid role change.');
            redirect('users/index');
        }
        $s = db()->prepare('UPDATE users SET role=? WHERE user_id=?');
        $s->execute([$role, $id]);
        flash('success', 'Role updated.');
        redirect('users/index');
    }

    public static function delete(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $u = self::find((int)($_POST['id'] ?? 0));
        if (!$u || !self::canManage($u)) {
            http_response_code(403);
            exit('You cannot delete this account.');
        }
        $s = db()->prepare('DELETE FROM users WHERE user_id = ?');
        $s->execute([$u['user_id']]);
        flash('success', 'User deleted.');
        redirect('users/index');
    }

    /* ---------------- My profile (every logged-in user, own account only) ---------------- */

    public static function profile(): void
    {
        require_login();
        $u = self::find(current_user()['id']);
        $profile = true;
        view('users/form', ['target' => $u, 'profile' => $profile]);
    }

    public static function profileSave(): void
    {
        require_login();
        require_post();
        $id = current_user()['id'];
        [$err, $username, $email, $newPass] = self::collect($id);

        if ($newPass !== '') {                               // changing password needs the current one
            $s = db()->prepare('SELECT password_hash FROM users WHERE user_id = ?');
            $s->execute([$id]);
            $hash = $s->fetchColumn();
            if (!password_verify($_POST['current_password'] ?? '', (string)$hash)) {
                $err[] = 'Current password is incorrect.';
            }
        }
        if ($err) {
            flash('error', implode(' ', $err));
            redirect('profile/edit');
        }
        if ($newPass !== '') {
            $s = db()->prepare('UPDATE users SET username=?, email=?, password_hash=? WHERE user_id=?');
            $s->execute([$username, $email, password_hash($newPass, PASSWORD_DEFAULT), $id]);
        } else {
            $s = db()->prepare('UPDATE users SET username=?, email=? WHERE user_id=?');
            $s->execute([$username, $email, $id]);
        }
        $_SESSION['user']['username'] = $username;           // role is never changed here
        flash('success', 'Profile updated.');
        redirect('profile/edit');
    }

    /** Shared server-side validation. Returns [errors, username, email, newPassword]. */
    private static function collect(int $id): array
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $newPass  = $_POST['new_password'] ?? '';
        $err = [];
        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) $err[] = 'Username must be 3-30 letters, digits or underscores.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) $err[] = 'Enter a valid email.';
        if ($newPass !== '' && strlen($newPass) < 8) $err[] = 'New password must be at least 8 characters.';
        if (!$err) {
            $s = db()->prepare('SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id <> ?');
            $s->execute([$username, $email, $id]);
            if ($s->fetch()) $err[] = 'Username or email already in use.';
        }
        return [$err, $username, $email, $newPass];
    }
}
