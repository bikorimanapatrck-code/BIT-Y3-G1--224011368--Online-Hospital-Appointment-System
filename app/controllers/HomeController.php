<?php
class HomeController
{
    public static function index(): void
    {
        $hospitals = db()->query('SELECT hospital_id, hospital_name, location, category, services, phone, building_image FROM hospitals WHERE is_available = 1 ORDER BY hospital_name LIMIT 6')->fetchAll();
        $me = current_user();
        if (!$me) {
            view('home/index', ['stats' => null, 'hospitals' => $hospitals]);
            return;
        }

        $stats = [
            'appointments' => (int)db()->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
            'doctors' => (int)db()->query('SELECT COUNT(*) FROM doctors')->fetchColumn(),
            'hospitals' => (int)db()->query('SELECT COUNT(*) FROM hospitals WHERE is_available = 1')->fetchColumn(),
        ];
        if (is_staff()) {
            $stats['patients'] = (int)db()->query('SELECT COUNT(*) FROM patients')->fetchColumn();
            $stats['users'] = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
        }
        view('home/index', compact('stats', 'hospitals'));
    }
}
