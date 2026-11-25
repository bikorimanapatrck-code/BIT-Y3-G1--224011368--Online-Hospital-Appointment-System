<div class="head">
  <div><h1>Available hospitals</h1><p class="muted">Browse hospital locations, categories, and services.</p></div>
  <?php if (current_user()['role'] === 'admin'): ?><a class="btn" href="index.php?r=hospitals/form">+ Add hospital</a><?php endif; ?>
</div>

<?php if (!$rows): ?>
  <section class="card empty-state"><h2>No hospitals listed yet</h2><p>Hospitals added by an administrator will appear here.</p></section>
<?php else: ?>
  <section class="hospital-grid">
    <?php foreach ($rows as $hospital): ?>
      <article class="hospital-card <?= !(int)$hospital['is_available'] ? 'unavailable' : '' ?>">
        <?php if (!empty($hospital['building_image'])): ?><div class="hospital-photo-frame"><img class="hospital-building" src="<?= e($hospital['building_image']) ?>" alt="Building of <?= e($hospital['hospital_name']) ?>"></div><?php endif; ?>
        <div class="hospital-card-top">
          <span class="category-pill"><?= e($hospital['category']) ?></span>
          <span class="availability <?= (int)$hospital['is_available'] ? 'available' : 'unavailable-label' ?>">
            <?= (int)$hospital['is_available'] ? 'Available' : 'Not currently available' ?>
          </span>
        </div>
        <h2><?= e($hospital['hospital_name']) ?></h2>
        <p class="hospital-location"><span aria-hidden="true">⌖</span> <?= e($hospital['location']) ?></p>
        <h3>Services</h3>
        <p class="hospital-services"><?= nl2br(e($hospital['services'])) ?></p>
        <?php if ($hospital['phone']): ?><p class="muted">Phone: <?= e($hospital['phone']) ?></p><?php endif; ?>
        <?php if (current_user()['role'] === 'admin'): ?>
          <div class="hospital-actions">
            <a class="btn small" href="index.php?r=hospitals/form&id=<?= (int)$hospital['hospital_id'] ?>">Edit</a>
            <form method="post" action="index.php?r=hospitals/delete" class="inline" data-confirm="Remove this hospital from the directory?">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$hospital['hospital_id'] ?>">
              <button class="btn small danger" type="submit">Delete</button>
            </form>
          </div>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
