<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';

try {
    $db = new DB_con();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $id = positiveId($_GET['id'] ?? null);
        if ($id > 0) {
            $product = $db->fetchProduct($id);
            if (!$product) respond(['success' => false, 'message' => 'Product not found.'], 404);
            respond(['success' => true, 'data' => $product]);
        }
        respond(['success' => true, 'data' => $db->fetchProducts()]);
    }

    if ($method === 'POST') {
        $data = requestData();
        $id = positiveId($data['id'] ?? null);
        $name = trim((string) ($data['name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $price = filter_var($data['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($data['quantity'] ?? null, FILTER_VALIDATE_INT);
        $categoryId = positiveId($data['category_id'] ?? null);

        if ($name === '' || $price === false || $price < 0 || $quantity === false || $quantity < 0 || $categoryId < 1) {
            respond(['success' => false, 'message' => 'Please provide a name, valid price, quantity, and category.'], 422);
        }

        $oldProduct = $id > 0 ? $db->fetchProduct($id) : false;
        if ($id > 0 && !$oldProduct) respond(['success' => false, 'message' => 'Product not found.'], 404);

        $newImage = uploadProductImage($_FILES['image'] ?? null);
        if ($newImage === false) {
            respond(['success' => false, 'message' => 'Image must be JPG, PNG, GIF, or WEBP and no larger than 2 MB.'], 422);
        }

        $image = $newImage ?? ($oldProduct['image'] ?? null);

        if ($id > 0) {
            $db->updateProduct($id, $name, $description !== '' ? $description : null, (float) $price, (int) $quantity, $categoryId, $image);
            if ($newImage !== null) removeProductImage($oldProduct['image'] ?? null);
            respond(['success' => true, 'message' => 'Product updated successfully.']);
        }

        $db->createProduct($name, $description !== '' ? $description : null, (float) $price, (int) $quantity, $categoryId, $image);
        respond(['success' => true, 'message' => 'Product created successfully.'], 201);
    }

    if ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $input);
        $id = positiveId($_GET['id'] ?? $input['id'] ?? null);
        if ($id < 1) respond(['success' => false, 'message' => 'A valid product ID is required.'], 422);

        $product = $db->fetchProduct($id);
        if (!$product) respond(['success' => false, 'message' => 'Product not found.'], 404);

        $db->deleteProduct($id);
        removeProductImage($product['image'] ?? null);
        respond(['success' => true, 'message' => 'Product deleted successfully.']);
    }

    header('Allow: GET, POST, DELETE');
    respond(['success' => false, 'message' => 'Method not allowed.'], 405);
} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(['success' => false, 'message' => 'Server error. Check PHP error log and database settings.'], 500);
}
