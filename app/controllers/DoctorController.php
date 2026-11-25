<?php
class DoctorController
{
    public static function index(): void
    {
        require_login();
        $rows = db()->query('SELECT * FROM doctors ORDER BY full_name')->fetchAll();
        view('doctors/index', compact('rows'));
    }

    public static function form(): void
    {
        require_role(['admin', 'staff']);
        $d = null;
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $s = db()->prepare('SELECT * FROM doctors WHERE doctor_id = ?');
            $s->execute([$id]);
            $d = $s->fetch();
            if (!$d) { http_response_code(404); exit('Doctor not found.'); }
        }
        view('doctors/form', compact('d'));
    }

    public static function save(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $id = (int)($_POST['doctor_id'] ?? 0);
        $d = [
            trim($_POST['full_name'] ?? ''),
            trim($_POST['specialization'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['department'] ?? ''),
        ];
        $experienceRaw = trim((string)($_POST['experience_years'] ?? ''));
        $experienceYears = ctype_digit($experienceRaw) ? (int)$experienceRaw : -1;
        $err = [];
        if ($d[0] === '' || strlen($d[0]) > 100) $err[] = 'Full name is required (max 100).';
        if ($d[1] === '' || strlen($d[1]) > 80)  $err[] = 'Specialization is required (max 80).';
        if (!preg_match('/^\+?[0-9]{7,15}$/', $d[2])) $err[] = 'Phone must be 7-15 digits.';
        if ($d[3] === '' || strlen($d[3]) > 80)  $err[] = 'Department is required (max 80).';
        if ($experienceYears < 0 || $experienceYears > 70) $err[] = 'Experience must be a whole number from 0 to 70 years.';
        if ($err) {
            flash('error', implode(' ', $err));
            redirect('doctors/form' . ($id ? "&id=$id" : ''));
        }
        if ($id) {
            $s = db()->prepare('UPDATE doctors SET full_name=?, specialization=?, phone=?, department=?, experience_years=? WHERE doctor_id=?');
            $s->execute([...$d, $experienceYears, $id]);
            flash('success', 'Doctor updated.');
        } else {
            $s = db()->prepare('INSERT INTO doctors (full_name, specialization, phone, department, experience_years) VALUES (?,?,?,?,?)');
            $s->execute([...$d, $experienceYears]);
            flash('success', 'Doctor added.');
        }
        redirect('doctors/index');
    }

    public static function delete(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $s = db()->prepare('DELETE FROM doctors WHERE doctor_id = ?');
        $s->execute([(int)($_POST['id'] ?? 0)]);
        flash('success', 'Doctor deleted.');
        redirect('doctors/index');
    }
}
