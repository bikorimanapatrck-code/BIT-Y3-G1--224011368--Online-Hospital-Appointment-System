<?php
class AppointmentController
{
    private const STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    public static function index(): void
    {
        require_login();
        $sql =
            'SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name
             FROM appointments a
             JOIN patients p ON p.patient_id = a.patient_id
             JOIN doctors d  ON d.doctor_id  = a.doctor_id
             ' . (is_staff() ? '' : 'WHERE p.user_id = ? ') . 'ORDER BY a.appointment_date DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute(is_staff() ? [] : [current_user()['id']]);
        $rows = $stmt->fetchAll();
        view('appointments/index', compact('rows'));
    }

    public static function form(): void
    {
        require_login();
        $a = null;
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            require_role(['admin', 'staff']); // only staff can edit
            $s = db()->prepare('SELECT * FROM appointments WHERE appointment_id = ?');
            $s->execute([$id]);
            $a = $s->fetch();
            if (!$a) { http_response_code(404); exit('Appointment not found.'); }
        }
        if (is_staff()) {
            $patients = db()->query('SELECT patient_id, full_name FROM patients ORDER BY full_name')->fetchAll();
        } else {
            $stmt = db()->prepare('SELECT patient_id, full_name FROM patients WHERE user_id = ? ORDER BY full_name');
            $stmt->execute([current_user()['id']]);
            $patients = $stmt->fetchAll();
            if (!$patients) { http_response_code(403); exit('Your patient profile is missing. Please contact hospital staff.'); }
        }
        $doctors  = db()->query('SELECT doctor_id, full_name, specialization, experience_years FROM doctors ORDER BY full_name')->fetchAll();
        $statuses = self::STATUSES;
        view('appointments/form', compact('a', 'patients', 'doctors', 'statuses'));
    }

    public static function save(): void
    {
        require_login();
        require_post();
        $id = (int)($_POST['appointment_id'] ?? 0);
        if ($id) require_role(['admin', 'staff']);

        $patient = (int)($_POST['patient_id'] ?? 0);
        if (!is_staff()) {
            $owned = db()->prepare('SELECT patient_id FROM patients WHERE user_id = ?');
            $owned->execute([current_user()['id']]);
            $patient = (int)$owned->fetchColumn();
        }
        $doctor  = (int)($_POST['doctor_id'] ?? 0);
        $dateRaw = str_replace('T', ' ', trim($_POST['appointment_date'] ?? ''));
        $notes   = trim($_POST['notes'] ?? '');
        // Patients can only create 'pending' appointments; staff may choose a status.
        $status  = is_staff() ? ($_POST['status'] ?? 'pending') : 'pending';

        $err = [];
        $dt = DateTime::createFromFormat('Y-m-d H:i', $dateRaw);
        if (!$dt || $dt->format('Y-m-d H:i') !== $dateRaw) {
            $err[] = 'Enter a valid appointment date and time.';
        } elseif (!$id && $dt < new DateTime()) {
            $err[] = 'Appointment must be in the future.';
        }
        if (!in_array($status, self::STATUSES, true)) $err[] = 'Invalid status.';
        if (strlen($notes) > 255) $err[] = 'Notes max 255 characters.';

        $c = db()->prepare('SELECT (SELECT COUNT(*) FROM patients WHERE patient_id=?) + (SELECT COUNT(*) FROM doctors WHERE doctor_id=?) AS n');
        $c->execute([$patient, $doctor]);
        if ((int)$c->fetch()['n'] !== 2) $err[] = 'Choose a valid patient and doctor.';

        if (!$err) {
            $dateSql = $dt->format('Y-m-d H:i:00');
            $q = db()->prepare('SELECT 1 FROM appointments WHERE doctor_id=? AND appointment_date=? AND appointment_id<>? AND status<>"cancelled"');
            $q->execute([$doctor, $dateSql, $id]);
            if ($q->fetch()) $err[] = 'That doctor is already booked at this time.';
        }
        if ($err) {
            flash('error', implode(' ', $err));
            redirect('appointments/form' . ($id ? "&id=$id" : ''));
        }

        try {
            if ($id) {
                $s = db()->prepare('UPDATE appointments SET patient_id=?, doctor_id=?, appointment_date=?, status=?, notes=? WHERE appointment_id=?');
                $s->execute([$patient, $doctor, $dateSql, $status, $notes, $id]);
                flash('success', 'Appointment updated.');
            } else {
                $s = db()->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes) VALUES (?,?,?,?,?)');
                $s->execute([$patient, $doctor, $dateSql, $status, $notes]);
                flash('success', 'Appointment booked.');
            }
        } catch (PDOException $ex) {
            flash('error', 'That time slot is not available.');
            redirect('appointments/form' . ($id ? "&id=$id" : ''));
        }
        redirect('appointments/index');
    }

    public static function delete(): void
    {
        require_role(['admin', 'staff']);
        require_post();
        $s = db()->prepare('DELETE FROM appointments WHERE appointment_id = ?');
        $s->execute([(int)($_POST['id'] ?? 0)]);
        flash('success', 'Appointment deleted.');
        redirect('appointments/index');
    }
}
