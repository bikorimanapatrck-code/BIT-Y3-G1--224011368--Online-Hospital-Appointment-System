<section class="card narrow">
  <h1><?= $p ? 'Edit' : 'Add' ?> patient</h1>
  <form method="post" action="index.php?r=patients/save" data-validate novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="patient_id" value="<?= (int)($p['patient_id'] ?? 0) ?>">
    <label>Full name <input name="full_name" required maxlength="100" value="<?= e($p['full_name'] ?? '') ?>"></label>
    <label>Date of birth <input type="date" name="dob" required max="<?= date('Y-m-d') ?>" value="<?= e($p['dob'] ?? '') ?>"></label>
    <label>Gender
      <select name="gender" required>
        <option value="">-- choose --</option>
        <?php foreach (['Male','Female','Other'] as $g): ?>
          <option <?= ($p['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Phone <input name="phone" required pattern="\+?[0-9]{7,15}" title="7-15 digits" value="<?= e($p['phone'] ?? '') ?>"></label>
    <button class="btn" type="submit">Save</button> <a href="index.php?r=patients/index">Cancel</a>
  </form>
</section>
