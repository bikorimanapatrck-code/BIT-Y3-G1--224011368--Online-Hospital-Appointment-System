<section class="card narrow portal">
  <div class="portal-badge">Staff &amp; Admin Portal</div>
  <h1>Administrator / Staff Login</h1>
  <p class="muted">Authorised hospital personnel only. Patients, please use the <a href="index.php?r=auth/login">patient login</a>.</p>
  <form method="post" action="index.php?r=auth/staff" data-validate novalidate>
    <?= csrf_field() ?>
    <label>Username <input name="username" required autocomplete="username"></label>
    <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn" type="submit">Sign in to portal</button>
  </form>
</section>
