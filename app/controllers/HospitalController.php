<?php
class HospitalController
{
    private const MAX_IMAGE_BYTES = 5242880;

    private static function saveBuildingImage(array $file, array &$errors): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errors[] = 'The building photo could not be uploaded.';
            return null;
        }
        if (($file['size'] ?? 0) > self::MAX_IMAGE_BYTES || !is_uploaded_file($file['tmp_name'] ?? '')) {
            $errors[] = 'Choose an image no larger than 5 MB.';
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) {
            $errors[] = 'Building photo must be a JPG, PNG, or WebP image.';
            return null;
        }

        $folder = __DIR__ . '/../../public/uploads/hospitals';
        if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
            $errors[] = 'The server could not prepare the photo folder.';
            return null;
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $folder . DIRECTORY_SEPARATOR . $filename)) {
            $errors[] = 'The building photo could not be saved.';
            return null;
        }
        return 'uploads/hospitals/' . $filename;
    }

    private static function removeBuildingImage(?string $relativePath): void
    {
        if (!$relativePath || !str_starts_with($relativePath, 'uploads/hospitals/')) return;
        $filename = basename($relativePath);
        if ($relativePath !== 'uploads/hospitals/' . $filename) return;
        $path = __DIR__ . '/../../public/uploads/hospitals/' . $filename;
        if (is_file($path)) unlink($path);
    }

    public static function index(): void
    {
        require_login();
        $rows = db()->query('SELECT * FROM hospitals ORDER BY is_available DESC, hospital_name')->fetchAll();
        view('hospitals/index', compact('rows'));
    }

    public static function form(): void
    {
        require_role(['admin']);
        $hospital = null;
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $s = db()->prepare('SELECT * FROM hospitals WHERE hospital_id = ?');
            $s->execute([$id]);
            $hospital = $s->fetch();
            if (!$hospital) { http_response_code(404); exit('Hospital not found.'); }
        }
        view('hospitals/form', compact('hospital'));
    }

    public static function save(): void
    {
        require_role(['admin']);
        require_post();
        $id = (int)($_POST['hospital_id'] ?? 0);
        $name = trim($_POST['hospital_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $services = trim($_POST['services'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $available = isset($_POST['is_available']) ? 1 : 0;
        $oldImage = null;
        if ($id) {
            $lookup = db()->prepare('SELECT building_image FROM hospitals WHERE hospital_id = ?');
            $lookup->execute([$id]);
            $oldImage = $lookup->fetchColumn();
            if ($oldImage === false) { http_response_code(404); exit('Hospital not found.'); }
        }
        $errors = [];
        if ($name === '' || mb_strlen($name) > 150) $errors[] = 'Hospital name is required (max 150 characters).';
        if ($location === '' || mb_strlen($location) > 255) $errors[] = 'Location is required (max 255 characters).';
        if ($category === '' || mb_strlen($category) > 80) $errors[] = 'Category is required (max 80 characters).';
        if ($services === '' || mb_strlen($services) > 5000) $errors[] = 'List the hospital services (max 5000 characters).';
        if ($phone !== '' && !preg_match('/^\+?[0-9 ()-]{7,25}$/', $phone)) $errors[] = 'Enter a valid phone number.';
        $newImage = null;
        if (isset($_FILES['building_image']) && is_array($_FILES['building_image'])) {
            $newImage = self::saveBuildingImage($_FILES['building_image'], $errors);
        }
        if ($errors) {
            if ($newImage) self::removeBuildingImage($newImage);
            flash('error', implode(' ', $errors));
            redirect('hospitals/form' . ($id ? "&id=$id" : ''));
        }

        $imagePath = $newImage ?: $oldImage;

        if ($id) {
            $s = db()->prepare('UPDATE hospitals SET hospital_name=?, location=?, category=?, services=?, phone=?, building_image=?, is_available=? WHERE hospital_id=?');
            $s->execute([$name, $location, $category, $services, $phone ?: null, $imagePath, $available, $id]);
            flash('success', 'Hospital updated.');
        } else {
            $s = db()->prepare('INSERT INTO hospitals (hospital_name, location, category, services, phone, building_image, is_available) VALUES (?,?,?,?,?,?,?)');
            $s->execute([$name, $location, $category, $services, $phone ?: null, $imagePath, $available]);
            flash('success', 'Hospital added to the directory.');
        }
        if ($newImage && $oldImage) self::removeBuildingImage($oldImage);
        redirect('hospitals/index');
    }

    public static function delete(): void
    {
        require_role(['admin']);
        require_post();
        $id = (int)($_POST['id'] ?? 0);
        $s = db()->prepare('SELECT building_image FROM hospitals WHERE hospital_id = ?');
        $s->execute([$id]);
        $image = $s->fetchColumn();
        $s = db()->prepare('DELETE FROM hospitals WHERE hospital_id = ?');
        $s->execute([$id]);
        if ($image) self::removeBuildingImage((string)$image);
        flash('success', 'Hospital removed.');
        redirect('hospitals/index');
    }
}
