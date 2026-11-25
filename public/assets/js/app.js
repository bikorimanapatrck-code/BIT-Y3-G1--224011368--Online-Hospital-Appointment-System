// 1) Client-side form validation (server-side validation is still the authority)
document.querySelectorAll('form[data-validate]').forEach(function (form) {
  form.addEventListener('submit', function (ev) {
    let ok = true;
    form.querySelectorAll('.field-error').forEach(function (n) { n.remove(); });
    form.querySelectorAll('input, select, textarea').forEach(function (f) {
      f.classList.remove('invalid');
      if (f.type === 'hidden' || f.disabled) return;
      if (!f.checkValidity()) {
        ok = false;
        f.classList.add('invalid');
        const msg = document.createElement('span');
        msg.className = 'field-error';
        msg.textContent = f.title || f.validationMessage;
        f.insertAdjacentElement('afterend', msg);
      }
    });
    if (!ok) ev.preventDefault();
  });
});

// 2) Confirm before delete
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
  form.addEventListener('submit', function (ev) {
    if (!confirm(form.dataset.confirm)) ev.preventDefault();
  });
});

// 3) Live search with Fetch + JSON API
const box = document.getElementById('live-search');
if (box) {
  const table = document.getElementById('appt-table');
  const tbody = table.querySelector('tbody');
  let timer;

  function cell(text) {
    const td = document.createElement('td');
    td.textContent = text ?? '';   // textContent => safe from XSS
    return td;
  }

  function render(rows, isStaff) {
    tbody.innerHTML = '';
    if (!rows.length) {
      const tr = document.createElement('tr');
      const td = cell('No matching appointments.');
      td.colSpan = isStaff ? 7 : 6;
      tr.appendChild(td);
      tbody.appendChild(tr);
      return;
    }
    rows.forEach(function (r) {
      const tr = document.createElement('tr');
      tr.appendChild(cell(r.appointment_id));
      tr.appendChild(cell(r.patient_name));
      tr.appendChild(cell(r.doctor_name));
      tr.appendChild(cell(r.appointment_date));
      const st = document.createElement('td');
      const b = document.createElement('span');
      b.className = 'badge ' + String(r.status).replace(/[^a-z]/g, '');
      b.textContent = r.status;
      st.appendChild(b);
      tr.appendChild(st);
      tr.appendChild(cell(r.notes));
      if (isStaff) {
        const td = document.createElement('td');
        td.className = 'actions';
        const a = document.createElement('a');
        a.className = 'btn small';
        a.href = 'index.php?r=appointments/form&id=' + encodeURIComponent(r.appointment_id);
        a.textContent = 'Edit';
        td.appendChild(a);

        const f = document.createElement('form');
        f.method = 'post';
        f.action = 'index.php?r=appointments/delete';
        f.className = 'inline';
        [['csrf', table.dataset.csrf], ['id', r.appointment_id]].forEach(function (pair) {
          const h = document.createElement('input');
          h.type = 'hidden'; h.name = pair[0]; h.value = pair[1];
          f.appendChild(h);
        });
        const del = document.createElement('button');
        del.type = 'submit'; del.className = 'btn small danger'; del.textContent = 'Delete';
        f.appendChild(del);
        f.addEventListener('submit', function (ev) {
          if (!confirm('Delete this appointment?')) ev.preventDefault();
        });
        td.appendChild(f);
        tr.appendChild(td);
      }
      tbody.appendChild(tr);
    });
  }

  box.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      fetch('index.php?r=api/search&q=' + encodeURIComponent(box.value))
        .then(function (res) { if (!res.ok) throw new Error('Request failed'); return res.json(); })
        .then(function (json) { render(json.data, json.is_staff); })
        .catch(function () {
          tbody.innerHTML = '<tr><td colspan="7">Search failed. Please try again.</td></tr>';
        });
    }, 250); // debounce
  });
}
