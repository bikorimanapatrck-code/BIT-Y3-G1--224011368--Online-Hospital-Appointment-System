<?php
declare(strict_types=1);
require __DIR__ . '/../app/core/bootstrap.php';

foreach (['Auth', 'Home', 'Hospital', 'Patient', 'Doctor', 'Appointment', 'User', 'Api'] as $c) {
    require __DIR__ . "/../app/controllers/{$c}Controller.php";
}

// Whitelisted routes: "controller/action" => [Class, method]
$routes = [
    'home/index'          => ['HomeController', 'index'],
    'hospitals/index'     => ['HospitalController', 'index'],
    'hospitals/form'      => ['HospitalController', 'form'],
    'hospitals/save'      => ['HospitalController', 'save'],
    'hospitals/delete'    => ['HospitalController', 'delete'],
    'auth/login'          => ['AuthController', 'login'],
    'auth/staff'          => ['AuthController', 'staffLogin'],
    'auth/register'       => ['AuthController', 'register'],
    'auth/logout'         => ['AuthController', 'logout'],
    'patients/index'      => ['PatientController', 'index'],
    'patients/form'       => ['PatientController', 'form'],
    'patients/save'       => ['PatientController', 'save'],
    'patients/delete'     => ['PatientController', 'delete'],
    'doctors/index'       => ['DoctorController', 'index'],
    'doctors/form'        => ['DoctorController', 'form'],
    'doctors/save'        => ['DoctorController', 'save'],
    'doctors/delete'      => ['DoctorController', 'delete'],
    'appointments/index'  => ['AppointmentController', 'index'],
    'appointments/form'   => ['AppointmentController', 'form'],
    'appointments/save'   => ['AppointmentController', 'save'],
    'appointments/delete' => ['AppointmentController', 'delete'],
    'users/index'         => ['UserController', 'index'],
    'users/form'          => ['UserController', 'form'],
    'users/save'          => ['UserController', 'save'],
    'users/role'          => ['UserController', 'role'],
    'users/delete'        => ['UserController', 'delete'],
    'profile/edit'        => ['UserController', 'profile'],
    'profile/save'        => ['UserController', 'profileSave'],
    'api/search'          => ['ApiController', 'search'],
];

$r = $_GET['r'] ?? 'home/index';
if (!isset($routes[$r])) {
    http_response_code(404);
    exit('Page not found.');
}
[$class, $method] = $routes[$r];
$class::$method();
