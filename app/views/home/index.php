<?php if (!$stats): ?>
  <section class="welcome card">
    <span class="portal-badge">MediBook</span>
    <h1>Online Hospital Appointment System</h1>
    <p>Find a doctor, book an appointment, and manage your care in one place.</p>
    <div class="home-actions">
      <a class="btn" href="index.php?r=auth/login">Sign in</a>
      <a class="btn btn-secondary" href="index.php?r=auth/register">Create patient account</a>
      <a class="text-link" href="index.php?r=auth/staff">Staff / Admin portal</a>
    </div>
  </section>
<?php else: ?>
  <section class="dashboard-intro">
    <div><span class="portal-badge"><?= is_staff() ? 'Staff dashboard' : 'Patient portal' ?></span>
      <h1>Welcome, <?= e(current_user()['username']) ?></h1>
      <p class="muted">Choose a section to continue.</p>
    </div>
    <a class="btn" href="index.php?r=appointments/form">+ Book appointment</a>
  </section>

  <section class="stat-grid" aria-label="System overview">
    <a class="stat-card" href="index.php?r=appointments/index"><span>Appointments</span><strong><?= (int)$stats['appointments'] ?></strong><small>View schedule</small></a>
    <a class="stat-card" href="index.php?r=doctors/index"><span>Doctors</span><strong><?= (int)$stats['doctors'] ?></strong><small>Browse doctors</small></a>
    <a class="stat-card" href="index.php?r=hospitals/index"><span>Available hospitals</span><strong><?= (int)$stats['hospitals'] ?></strong><small>View locations and services</small></a>
    <?php if (isset($stats['patients'])): ?>
      <a class="stat-card" href="index.php?r=patients/index"><span>Patients</span><strong><?= (int)$stats['patients'] ?></strong><small>Manage patient records</small></a>
      <a class="stat-card" href="index.php?r=users/index"><span>Users</span><strong><?= (int)$stats['users'] ?></strong><small>Manage accounts</small></a>
    <?php endif; ?>
  </section>

  <section class="quick-links card">
    <h2>Quick links</h2>
    <div class="home-actions">
      <a class="btn" href="index.php?r=appointments/index">Appointments</a>
      <a class="btn btn-secondary" href="index.php?r=doctors/index">Doctors</a>
      <a class="btn btn-secondary" href="index.php?r=hospitals/index">Hospitals</a>
      <?php if (is_staff()): ?>
        <a class="btn btn-secondary" href="index.php?r=patients/index">Patients</a>
        <a class="btn btn-secondary" href="index.php?r=users/index">Users</a>
      <?php endif; ?>
      <a class="text-link" href="index.php?r=profile/edit">My profile</a>
    </div>
  </section>
<?php endif; ?>

<section class="dashboard-hospitals">
  <div class="head">
    <div><h2>Available hospitals</h2><p class="muted">Locations, categories, and services near you.</p></div>
    <div class="home-actions">
      <a class="btn btn-secondary" href="index.php?r=hospitals/index">View all hospitals</a>
      <?php if (current_user() && current_user()['role'] === 'admin'): ?><a class="btn" href="index.php?r=hospitals/form">+ Add hospital</a><?php endif; ?>
    </div>
  </div>
  <?php if (!$hospitals): ?>
    <div class="card empty-state"><p>No hospitals are listed yet. An administrator can add hospitals from this dashboard.</p></div>
  <?php else: ?>
    <div class="hospital-grid">
      <?php foreach ($hospitals as $hospital): ?>
        <article class="hospital-card">
          <?php if (!empty($hospital['building_image'])): ?><div class="hospital-photo-frame"><img class="hospital-building" src="<?= e($hospital['building_image']) ?>" alt="Building of <?= e($hospital['hospital_name']) ?>"></div><?php endif; ?>
          <span class="category-pill"><?= e($hospital['category']) ?></span>
          <h3><?= e($hospital['hospital_name']) ?></h3>
          <p class="hospital-location"><span aria-hidden="true">⌖</span> <?= e($hospital['location']) ?></p>
          <p class="hospital-services"><?= e($hospital['services']) ?></p>
          <?php if ($hospital['phone']): ?><p class="muted">Phone: <?= e($hospital['phone']) ?></p><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
