</main>
<footer class="site-footer" style="display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;text-align:center!important;width:100%;margin:2rem auto 0;padding:1rem;background:#12394a;color:#fff;">
  <?php if ($me = current_user()): ?>
    <span>Signed in as <?= e($me['username']) ?></span>
    <form method="post" action="index.php?r=auth/logout" class="inline" style="display:flex!important;justify-content:center!important;align-items:center!important;width:100%;margin:0 auto;">
      <?= csrf_field() ?>
      <button class="btn small danger" type="submit">Logout</button>
    </form>
  <?php else: ?>
    <span>Online Hospital Appointment System</span>
  <?php endif; ?>
</footer>
<script src="assets/js/app.js"></script>
</body>
</html>
