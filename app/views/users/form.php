<section class="card narrow">
  <h1><?= $profile ? 'Edit my profile' : 'Edit user' ?></h1>
  <form method="post" action="index.php?r=<?= $profile ? 'profile/save' : 'users/save' ?>" data-validate novalidate>
    <?= csrf_field() ?>
    <?php if (!$profile): ?><input type="hidden" name="user_id" value="<?= (int)$target['user_id'] ?>"><?php endif; ?>
    <label>Username <input name="username" required pattern="[A-Za-z0-9_]{3,30}" title="3-30 letters, digits or underscores" value="<?= e($target['username']) ?>"></label>
    <label>Email <input type="email" name="email" required maxlength="100" value="<?= e($target['email']) ?>"></label>

    <?php if (!$profile && current_user()['role'] === 'admin'): ?>
    <label>Role
      <select name="role">
        <?php foreach (['admin','staff','patient'] as $ro): ?>
          <option <?= $target['role'] === $ro ? 'selected' : '' ?>><?= $ro ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php else: ?>
    <p class="muted">Role: <span class="badge role-<?= e($target['role']) ?>"><?= e($target['role']) ?></span> &middot; Member since <?= e($target['created_at']) ?></p>
    <?php endif; ?>

    <fieldset>
      <legend>Change password (optional)</legend>
      <?php if ($profile): ?>
      <label>Current password <input type="password" name="current_password" autocomplete="current-password"></label>
      <?php endif; ?>
      <label>New password (min 8) <input type="password" name="new_password" minlength="8" autocomplete="new-password" placeholder="Leave blank to keep current"></label>
    </fieldset>

    <button class="btn" type="submit">Save changes</button>
    <a href="index.php?r=<?= $profile ? 'appointments/index' : 'users/index' ?>">Cancel</a>
  </form>
</section>
