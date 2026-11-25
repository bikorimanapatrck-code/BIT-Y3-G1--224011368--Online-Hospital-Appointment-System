<div class="head"><h1>Patients</h1><a class="btn" href="index.php?r=patients/form">+ Add patient</a></div>
<div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>Name</th><th>DOB</th><th>Gender</th><th>Phone</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['patient_id'] ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['dob']) ?></td>
      <td><?= e($r['gender']) ?></td><td><?= e($r['phone']) ?></td>
      <td class="actions">
        <a class="btn small" href="index.php?r=patients/form&id=<?= (int)$r['patient_id'] ?>">Edit</a>
        <form method="post" action="index.php?r=patients/delete" class="inline" data-confirm="Delete this patient and their appointments?">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['patient_id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6">No patients yet.</td></tr><?php endif; ?>
  </tbody>
</table></div>
