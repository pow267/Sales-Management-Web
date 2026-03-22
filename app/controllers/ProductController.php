<?php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ProductController
{
    private ProductModel $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    private function sanitize(array $input, bool $isUpdate = false): array
    {
        $data = [
            'ten_sua' => trim($input['ten_sua'] ?? ''),
            'ma_hang_sua' => trim($input['ma_hang_sua'] ?? ''),
            'loai_sua' => trim($input['loai_sua'] ?? ''),
            'trong_luong' => (int)($input['trong_luong'] ?? 0),
            'don_gia' => (int)($input['don_gia'] ?? 0),
            'tpdd' => trim($input['tpdd'] ?? ''),
            'loi_ich' => trim($input['loi_ich'] ?? ''),
            'hinh' => ''
        ];

        if ($isUpdate) {
            $data['id'] = (int)($input['id'] ?? 0);
        }

        return $data;
    }

    private function handleUpload(): ?string
    {

        if (empty($_FILES['hinh']['name'])) {
            return null;
        }

        if ($_FILES['hinh']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['hinh']['tmp_name']);

        if (!in_array($mime, ['image/png', 'image/jpeg'])) {
            return null;
        }

        $ext = strtolower(pathinfo($_FILES['hinh']['name'], PATHINFO_EXTENSION));

        // 🔥 FIX: random filename
        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

        $targetPath = $uploadDir . $fileName;
        $tmpPath = $targetPath . '.tmp';

        // Move file
        if (!move_uploaded_file($_FILES['hinh']['tmp_name'], $tmpPath)) {
            return null;
        }

        // Validate image thật
        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo === false) {
            unlink($tmpPath);
            return null;
        }

        // Rename atomic
        if (!rename($tmpPath, $targetPath)) {
            unlink($tmpPath);
            return null;
        }

        // 🔥 VERIFY: đảm bảo file tồn tại
        if (!file_exists($targetPath)) {
            return null;
        }

        clearstatcache(true, $targetPath);

        return $fileName;
    }

    private function deleteImageFile(?string $fileName): void
    {
        if (empty($fileName)) return;

        $filePath = __DIR__ . '/../../public/assets/images/' . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function index(): void
    {
        AuthMiddleware::check();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $perPage = 9;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $perPage;

        // ================= ADD =================
        if (isset($_POST['btn_them'])) {

            AuthMiddleware::role('admin');

            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                exit('Invalid CSRF token');
            }

            $data = $this->sanitize($_POST, false);

            $uploadedFile = $this->handleUpload();

            if ($uploadedFile) {
                $fullPath = __DIR__ . '/../../public/assets/images/' . $uploadedFile;

                // 🔥 chỉ lưu DB nếu file tồn tại thật
                $data['hinh'] = file_exists($fullPath) ? $uploadedFile : '';
            } else {
                $data['hinh'] = '';
            }

            $new_id = $this->model->insert($data);

            $_SESSION['flash'] = "Thêm sản phẩm thành công!";

            header("Location: /?id=$new_id&page=$page#chitiet");
            exit;
        }

        // ================= UPDATE =================
        if (isset($_POST['btn_capnhat'])) {

            AuthMiddleware::role('admin');

            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                exit('Invalid CSRF token');
            }

            $data = $this->sanitize($_POST, true);

            $oldImage = $_POST['hinh_cu'] ?? '';

            $uploadedFile = $this->handleUpload();

            if ($uploadedFile) {
                $fullPath = __DIR__ . '/../../public/assets/images/' . $uploadedFile;

                if (file_exists($fullPath)) {
                    $this->deleteImageFile($oldImage);
                    $data['hinh'] = $uploadedFile;
                } else {
                    $data['hinh'] = $oldImage;
                }
            } else {
                $data['hinh'] = $oldImage;
            }

            $this->model->update($data);

            $_SESSION['flash'] = "Cập nhật thành công!";

            header("Location: /?id=" . $data['id'] . "&page=$page#chitiet");
            exit;
        }

        // ================= DELETE =================
        if (isset($_POST['btn_xoa'])) {

            AuthMiddleware::role('admin');

            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                exit('Invalid CSRF token');
            }

            $id = (int)$_POST['id'];

            $product = $this->model->getById($id);

            if ($product && !empty($product['hinh'])) {
                $this->deleteImageFile($product['hinh']);
            }

            $this->model->delete($id);

            $_SESSION['flash'] = "Xóa sản phẩm thành công!";

            header("Location: /?page=$page");
            exit;
        }

        // ================= VIEW =================
        $totalProducts = $this->model->countAll();
        $totalPages = max(1, ceil($totalProducts / $perPage));

        $products = $this->model->getPaginated($perPage, $offset);
        $hangSua = $this->model->getAllHangSua();

        $chitiet = isset($_GET['id'])
            ? $this->model->getById((int)$_GET['id'])
            : null;

        require __DIR__ . '/../views/list.php';
    }
}