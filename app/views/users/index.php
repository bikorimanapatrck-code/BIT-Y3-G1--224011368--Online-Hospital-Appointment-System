<h1>Users</h1>
<?php if (current_user()['role'] === 'staff'): ?>
<p class="muted">Staff can edit or delete patient accounts. Admin and staff accounts are managed by an administrator.</p>
<?php endif; ?>
<div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r):
      $self = (int)$r['user_id'] === current_user()['id'];
      $can  = UserController::canManage($r); ?>
    <tr>
      <td><?= (int)$r['user_id'] ?></td><td><?= e($r['username']) ?><?= $self ? ' <em>(you)</em>' : '' ?></td><td><?= e($r['email']) ?></td>
      <td>
        <?php if (current_user()['role'] === 'admin'): ?>
        <form method="post" action="index.php?r=users/role" class="inline">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['user_id'] ?>">
          <select name="role" <?= $self ? 'disabled' : '' ?> onchange="this.form.submit()">
            <?php foreach (['admin','staff','patient'] as $ro): ?>
              <option <?= $r['role'] === $ro ? 'selected' : '' ?>><?= $ro ?></option>
            <?php endforeach; ?>
          </select>
        </form>
        <?php else: ?><span class="badge role-<?= e($r['role']) ?>"><?= e($r['role']) ?></span><?php endif; ?>
      </td>
      <td><?= e($r['created_at']) ?></td>
      <td class="actions">
        <?php if ($self): ?>
          <a class="btn small" href="index.php?r=profile/edit">My profile</a>
        <?php elseif ($can): ?>
          <a class="btn small" href="index.php?r=users/form&id=<?= (int)$r['user_id'] ?>">Edit</a>
          <form method="post" action="index.php?r=users/delete" class="inline" data-confirm="Delete user <?= e($r['username']) ?>?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['user_id'] ?>">
            <button class="btn small danger" type="submit">Delete</button>
          </form>
        <?php else: ?><span class="muted">Admin only</span><?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
