<div class="head"><h1>Doctors</h1>
<?php if (is_staff()): ?><a class="btn" href="index.php?r=doctors/form">+ Add doctor</a><?php endif; ?></div>
<div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>Name</th><th>Specialization</th><th>Department</th><th>Experience</th><th>Phone</th><?php if (is_staff()): ?><th>Actions</th><?php endif; ?></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['doctor_id'] ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['specialization']) ?></td>
      <td><?= e($r['department']) ?></td><td><?= (int)$r['experience_years'] ?> years</td><td><?= e($r['phone']) ?></td>
      <?php if (is_staff()): ?>
      <td class="actions">
        <a class="btn small" href="index.php?r=doctors/form&id=<?= (int)$r['doctor_id'] ?>">Edit</a>
        <form method="post" action="index.php?r=doctors/delete" class="inline" data-confirm="Delete this doctor and their appointments?">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['doctor_id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
      </td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
