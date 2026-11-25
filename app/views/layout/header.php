<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Online hospital appointment booking system">
<title>Hospital Appointments</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php?r=home/index">+ MediBook</a>
  <nav aria-label="Main navigation">
  <a class="nav-home" href="index.php?r=home/index" aria-label="Home">Home</a>
  <a href="index.php?r=hospitals/index">Hospitals</a>
  <?php if ($me = current_user()): ?>
    <a href="index.php?r=appointments/index">Appointments</a>
    <a href="index.php?r=doctors/index">Doctors</a>
    <?php if (is_staff()): ?><a href="index.php?r=patients/index">Patients</a><?php endif; ?>
    <?php if (is_staff()): ?><a href="index.php?r=users/index">Users</a><?php endif; ?>
    <a href="index.php?r=profile/edit">My profile</a>
  <?php else: ?>
    <a href="index.php?r=auth/login">Login</a>
    <a href="index.php?r=auth/register">Register</a>
    <a href="index.php?r=auth/staff">Staff / Admin</a>
  <?php endif; ?>
  </nav>
</header>
<main class="container">
<?php if (!empty($flash)): ?>
  <div class="alert <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>
