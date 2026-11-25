<section class="card narrow">
  <h1>Register</h1>
  <form method="post" action="index.php?r=auth/register" data-validate novalidate>
    <?= csrf_field() ?>
    <label>Username <input name="username" required pattern="[A-Za-z0-9_]{3,30}" title="3-30 letters, digits or underscores"></label>
    <label>Email <input type="email" name="email" required></label>
    <label>Full name <input name="full_name" required maxlength="100"></label>
    <label>Date of birth <input type="date" name="dob" required max="<?= date('Y-m-d') ?>"></label>
    <label>Gender
      <select name="gender" required><option value="">-- choose --</option><option>Female</option><option>Male</option><option>Other</option></select>
    </label>
    <label>Phone <input name="phone" required pattern="\+?[0-9]{7,15}" title="7-15 digits"></label>
    <label>Password (min 8) <input type="password" name="password" required minlength="8"></label>
    <button class="btn" type="submit">Create patient account</button>
  </form>
</section>
