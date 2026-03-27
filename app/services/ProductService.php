<?php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/ValidationException.php';

class ProductService
{
    private ProductModel $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    public function list(int $page = 1, int $perPage = 9, string $search = ''): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $search = trim($search);

        $totalProducts = $this->model->countAll($search);
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        return [
            'items' => $this->mapProducts(
                $this->model->getPaginated($perPage, $offset, $search)
            ),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalProducts,
                'total_pages' => $totalPages,
                'search' => $search
            ]
        ];
    }

    public function getById(int $id): ?array
    {
        $product = $this->model->getById($id);
        return $product ? $this->mapProduct($product) : null;
    }

    public function getBrands(): array
    {
        return $this->model->getAllHangSua();
    }

    public function create(array $input, array $files = []): array
    {
        $data = $this->sanitize($input);
        $this->validate($data);

        $uploadedFile = $this->handleUpload($files);
        $data['hinh'] = $uploadedFile ?? 'default.png';

        $id = $this->model->insert($data);

        return $this->getById($id) ?? ['id' => $id];
    }

    public function update(int $id, array $input, array $files = []): array
    {
        $existing = $this->model->getById($id);

        if (!$existing) {
            throw new OutOfBoundsException('Sản phẩm không tồn tại.');
        }

        $data = $this->sanitize($input, true);
        $data['id'] = $id;
        $this->validate($data);

        $uploadedFile = $this->handleUpload($files);

        if ($uploadedFile !== null) {
            $this->deleteImageFile($existing['hinh'] ?? null);
            $data['hinh'] = $uploadedFile;
        } else {
            $data['hinh'] = $existing['hinh'] ?? 'default.png';
        }

        $this->model->update($data);

        return $this->getById($id) ?? ['id' => $id];
    }

    public function delete(int $id): void
    {
        $existing = $this->model->getById($id);

        if (!$existing) {
            throw new OutOfBoundsException('Sản phẩm không tồn tại.');
        }

        $this->deleteImageFile($existing['hinh'] ?? null);
        $this->model->delete($id);
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
            'hinh' => trim($input['hinh'] ?? '')
        ];

        if ($isUpdate) {
            $data['id'] = (int)($input['id'] ?? 0);
        }

        return $data;
    }

    private function validate(array $data): void
    {
        $errors = [];

        if ($data['ten_sua'] === '') {
            $errors['ten_sua'] = 'Tên sữa không được để trống.';
        }

        if ($data['ma_hang_sua'] === '') {
            $errors['ma_hang_sua'] = 'Hãng sữa không được để trống.';
        }

        if ($data['trong_luong'] <= 0) {
            $errors['trong_luong'] = 'Trọng lượng phải lớn hơn 0.';
        }

        if ($data['don_gia'] <= 0) {
            $errors['don_gia'] = 'Đơn giá phải lớn hơn 0.';
        }

        if (!empty($errors)) {
            throw new ValidationException(
                'Vui lòng kiểm tra lại thông tin sản phẩm.',
                $errors
            );
        }
    }

    private function handleUpload(array $files): ?string
    {
        if (
            !isset($files['hinh']) ||
            !is_array($files['hinh']) ||
            ($files['hinh']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if (($files['hinh']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new ValidationException(
                'Tải ảnh lên không thành công.',
                ['hinh' => 'Tải ảnh lên không thành công.']
            );
        }

        $uploadDir = __DIR__ . '/../../public/assets/images/';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Không thể tạo thư mục lưu ảnh.');
        }

        $tmpName = $files['hinh']['tmp_name'] ?? '';

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);

        if (!in_array($mime, ['image/png', 'image/jpeg'], true)) {
            throw new ValidationException(
                'Chỉ chấp nhận ảnh PNG hoặc JPEG.',
                ['hinh' => 'Chỉ chấp nhận ảnh PNG hoặc JPEG.']
            );
        }

        $ext = strtolower(pathinfo($files['hinh']['name'] ?? '', PATHINFO_EXTENSION));
        $ext = in_array($ext, ['png', 'jpg', 'jpeg'], true) ? $ext : 'png';

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        $moved = is_uploaded_file($tmpName)
            ? move_uploaded_file($tmpName, $targetPath)
            : rename($tmpName, $targetPath);

        if (!$moved) {
            throw new RuntimeException('Không thể lưu ảnh tải lên.');
        }

        return $fileName;
    }

    private function deleteImageFile(?string $fileName): void
    {
        $fileName = trim((string)$fileName);

        if ($fileName === '' || $fileName === 'default.png') {
            return;
        }

        $filePath = __DIR__ . '/../../public/assets/images/' . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    private function mapProducts(array $products): array
    {
        return array_map(fn(array $product): array => $this->mapProduct($product), $products);
    }

    private function mapProduct(array $product): array
    {
        $image = trim($product['hinh'] ?? '');
        $imagePath = __DIR__ . '/../../public/assets/images/' . $image;

        if ($image === '' || !file_exists($imagePath)) {
            $image = 'default.png';
        }

        $product['id'] = (int)($product['id'] ?? 0);
        $product['trong_luong'] = (int)($product['trong_luong'] ?? 0);
        $product['don_gia'] = (int)($product['don_gia'] ?? 0);
        $product['hinh'] = $image;
        $product['image_url'] = '/assets/images/' . rawurlencode($image);

        return $product;
    }
}
