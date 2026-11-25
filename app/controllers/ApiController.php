<?php
/** JSON endpoint: GET index.php?r=api/search&q=term  (live appointment search) */
class ApiController
{
    public static function search(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!current_user()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $q = '%' . trim($_GET['q'] ?? '') . '%';
        $s = db()->prepare(
            'SELECT a.appointment_id, a.appointment_date, a.status, a.notes,
                    p.full_name AS patient_name, d.full_name AS doctor_name
             FROM appointments a
             JOIN patients p ON p.patient_id = a.patient_id
             JOIN doctors d  ON d.doctor_id  = a.doctor_id
             WHERE (p.full_name LIKE ? OR d.full_name LIKE ? OR a.status LIKE ?) ' . (is_staff() ? '' : 'AND p.user_id = ? ') . '
             ORDER BY a.appointment_date DESC LIMIT 50'
        );
        $params = [$q, $q, $q];
        if (!is_staff()) $params[] = current_user()['id'];
        $s->execute($params);
        echo json_encode(['data' => $s->fetchAll(), 'is_staff' => is_staff()]);
    }
}
