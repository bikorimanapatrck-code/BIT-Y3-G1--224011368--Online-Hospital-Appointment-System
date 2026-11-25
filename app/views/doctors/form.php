<section class="card narrow">
  <h1><?= $d ? 'Edit' : 'Add' ?> doctor</h1>
  <form method="post" action="index.php?r=doctors/save" data-validate novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="doctor_id" value="<?= (int)($d['doctor_id'] ?? 0) ?>">
    <label>Full name <input name="full_name" required maxlength="100" value="<?= e($d['full_name'] ?? '') ?>"></label>
    <label>Specialization <input name="specialization" required maxlength="80" value="<?= e($d['specialization'] ?? '') ?>"></label>
    <label>Department <input name="department" required maxlength="80" value="<?= e($d['department'] ?? '') ?>"></label>
    <label>Years of experience <input type="number" name="experience_years" required min="0" max="70" step="1" value="<?= (int)($d['experience_years'] ?? 0) ?>"></label>
    <label>Phone <input name="phone" required pattern="\+?[0-9]{7,15}" title="7-15 digits" value="<?= e($d['phone'] ?? '') ?>"></label>
    <button class="btn" type="submit">Save</button> <a href="index.php?r=doctors/index">Cancel</a>
  </form>
</section>
