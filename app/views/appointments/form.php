<section class="card narrow">
  <h1><?= $a ? 'Edit' : 'Book' ?> appointment</h1>
  <form method="post" action="index.php?r=appointments/save" data-validate novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="appointment_id" value="<?= (int)($a['appointment_id'] ?? 0) ?>">
    <?php if (is_staff()): ?><label>Patient
      <select name="patient_id" required>
        <option value="">-- choose --</option>
        <?php foreach ($patients as $p): ?>
          <option value="<?= (int)$p['patient_id'] ?>" <?= (int)($a['patient_id'] ?? 0) === (int)$p['patient_id'] ? 'selected' : '' ?>><?= e($p['full_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><?php else: ?>
      <p><strong>Patient:</strong> <?= e($patients[0]['full_name']) ?></p>
      <input type="hidden" name="patient_id" value="<?= (int)$patients[0]['patient_id'] ?>">
    <?php endif; ?>
    <label>Doctor
      <select name="doctor_id" required>
        <option value="">-- choose --</option>
        <?php foreach ($doctors as $d): ?>
          <option value="<?= (int)$d['doctor_id'] ?>" <?= (int)($a['doctor_id'] ?? 0) === (int)$d['doctor_id'] ? 'selected' : '' ?>><?= e($d['full_name']) ?> (<?= e($d['specialization']) ?>, <?= (int)$d['experience_years'] ?> years' experience)</option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Date &amp; time
      <input type="datetime-local" name="appointment_date" required value="<?= $a ? e(date('Y-m-d\TH:i', strtotime($a['appointment_date']))) : '' ?>">
    </label>
    <?php if (is_staff()): ?>
    <label>Status
      <select name="status">
        <?php foreach ($statuses as $s): ?>
          <option <?= ($a['status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php endif; ?>
    <label>Notes <textarea name="notes" maxlength="255" rows="3"><?= e($a['notes'] ?? '') ?></textarea></label>
    <button class="btn" type="submit">Save</button> <a href="index.php?r=appointments/index">Cancel</a>
  </form>
</section>
