<section class="card narrow">
  <h1>Patient Login</h1>
  <form method="post" action="index.php?r=auth/login" data-validate novalidate>
    <?= csrf_field() ?>
    <label>Username <input name="username" required></label>
    <label>Password <input type="password" name="password" required></label>
    <button class="btn" type="submit">Login</button>
  </form>
  <p>No account? <a href="index.php?r=auth/register">Register</a></p>
  <p class="muted">Hospital staff or administrator? <a href="index.php?r=auth/staff">Go to the staff portal</a></p>
</section>
