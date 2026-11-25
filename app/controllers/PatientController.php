<?php
class PatientController
{
    public static function index(): void
    {
        require_role(['admin', 'staff']);
        $rows = db()->query('SELECT * FROM patients ORDER BY full_name')->fetchAll();
        view('patients/index', compact('rows'));
    }

    public static function form(): void
    {
        require_role(['admin', 'staff']);
        $p = null;
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $s = db()->prepare('SELECT * FROM patients WHERE patient_id = ?');
            $s->execute([$id]);
            $p = $s->fetch();
            if (!$p) { http_response_code(404); exit('Patient not found.'); }
        }
        view('patients/form', compact('p'));
    }

    public static function save(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $id = (int)($_POST['patient_id'] ?? 0);
        $d = [
            trim($_POST['full_name'] ?? ''),
            trim($_POST['dob'] ?? ''),
            trim($_POST['gender'] ?? ''),
            trim($_POST['phone'] ?? ''),
        ];
        $err = [];
        if ($d[0] === '' || strlen($d[0]) > 100) $err[] = 'Full name is required (max 100).';
        $dt = DateTime::createFromFormat('Y-m-d', $d[1]);
        if (!$dt || $dt->format('Y-m-d') !== $d[1] || $dt > new DateTime()) $err[] = 'Enter a valid date of birth in the past.';
        if (!in_array($d[2], ['Male', 'Female', 'Other'], true)) $err[] = 'Choose a gender.';
        if (!preg_match('/^\+?[0-9]{7,15}$/', $d[3])) $err[] = 'Phone must be 7-15 digits.';
        if ($err) {
            flash('error', implode(' ', $err));
            redirect('patients/form' . ($id ? "&id=$id" : ''));
        }
        if ($id) {
            $s = db()->prepare('UPDATE patients SET full_name=?, dob=?, gender=?, phone=? WHERE patient_id=?');
            $s->execute([...$d, $id]);
            flash('success', 'Patient updated.');
        } else {
            $s = db()->prepare('INSERT INTO patients (full_name, dob, gender, phone) VALUES (?,?,?,?)');
            $s->execute($d);
            flash('success', 'Patient added.');
        }
        redirect('patients/index');
    }

    public static function delete(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $s = db()->prepare('DELETE FROM patients WHERE patient_id = ?');
        $s->execute([(int)($_POST['id'] ?? 0)]);
        flash('success', 'Patient deleted.');
        redirect('patients/index');
    }
}
