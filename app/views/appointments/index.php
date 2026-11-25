<div class="head"><h1>Appointments</h1><a class="btn" href="index.php?r=appointments/form">+ Book appointment</a></div>
<input type="search" id="live-search" placeholder="Search patient, doctor or status..." autocomplete="off">
<div class="table-wrap"><table id="appt-table" data-csrf="<?= e(csrf_token()) ?>">
  <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th><th>Notes</th><?php if (is_staff()): ?><th>Actions</th><?php endif; ?></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['appointment_id'] ?></td><td><?= e($r['patient_name']) ?></td><td><?= e($r['doctor_name']) ?></td>
      <td><?= e($r['appointment_date']) ?></td><td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
      <td><?= e($r['notes']) ?></td>
      <?php if (is_staff()): ?>
      <td class="actions">
        <a class="btn small" href="index.php?r=appointments/form&id=<?= (int)$r['appointment_id'] ?>">Edit</a>
        <form method="post" action="index.php?r=appointments/delete" class="inline" data-confirm="Delete this appointment?">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['appointment_id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
      </td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="7">No appointments yet.</td></tr><?php endif; ?>
  </tbody>
</table></div>
