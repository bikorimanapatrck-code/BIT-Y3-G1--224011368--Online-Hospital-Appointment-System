<section class="card narrow">
  <h1><?= $hospital ? 'Edit' : 'Add' ?> hospital</h1>
  <form method="post" action="index.php?r=hospitals/save" enctype="multipart/form-data" data-validate novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="hospital_id" value="<?= (int)($hospital['hospital_id'] ?? 0) ?>">
    <label>Hospital name <input name="hospital_name" required maxlength="150" value="<?= e($hospital['hospital_name'] ?? '') ?>"></label>
    <label>Location / address <input name="location" required maxlength="255" placeholder="Area, city, or street address" value="<?= e($hospital['location'] ?? '') ?>"></label>
    <label>Hospital category <input name="category" required maxlength="80" placeholder="Public, Private, Specialist, Community..." value="<?= e($hospital['category'] ?? '') ?>"></label>
    <label>Services offered <textarea name="services" required maxlength="5000" rows="5" placeholder="For example: emergency care, maternity, cardiology"><?= e($hospital['services'] ?? '') ?></textarea></label>
    <label>Phone (optional) <input name="phone" maxlength="25" value="<?= e($hospital['phone'] ?? '') ?>"></label>
    <label>Hospital building photo <span class="muted">JPG, PNG, or WebP; up to 5 MB</span>
      <input type="file" name="building_image" accept="image/jpeg,image/png,image/webp">
    </label>
    <?php if (!empty($hospital['building_image'])): ?>
      <div class="image-preview"><span class="muted">Current building photo</span><img src="<?= e($hospital['building_image']) ?>" alt="Building of <?= e($hospital['hospital_name']) ?>"></div>
    <?php endif; ?>
    <label class="check-label"><input type="checkbox" name="is_available" value="1" <?= !isset($hospital) || (int)$hospital['is_available'] ? 'checked' : '' ?>> Show as currently available</label>
    <button class="btn" type="submit">Save hospital</button>
    <a href="index.php?r=hospitals/index">Cancel</a>
  </form>
</section>
