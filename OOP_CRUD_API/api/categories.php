<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';

try {
    $db = new DB_con();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $id = positiveId($_GET['id'] ?? null);
        if ($id > 0) {
            $category = $db->fetchCategory($id);
            if (!$category) respond(['success' => false, 'message' => 'Category not found.'], 404);
            respond(['success' => true, 'data' => $category]);
        }
        respond(['success' => true, 'data' => $db->fetchCategories()]);
    }

    if ($method === 'POST' || $method === 'PUT') {
        $data = requestData();
        $id = positiveId($data['id'] ?? $_GET['id'] ?? null);
        $name = trim((string) ($data['name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        if ($name === '') respond(['success' => false, 'message' => 'Category name is required.'], 422);

        if ($id > 0) {
            if (!$db->fetchCategory($id)) respond(['success' => false, 'message' => 'Category not found.'], 404);
            $db->updateCategory($id, $name, $description !== '' ? $description : null);
            respond(['success' => true, 'message' => 'Category updated successfully.']);
        }

        $db->createCategory($name, $description !== '' ? $description : null);
        respond(['success' => true, 'message' => 'Category created successfully.'], 201);
    }

    if ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $input);
        $id = positiveId($_GET['id'] ?? $input['id'] ?? null);
        if ($id < 1) respond(['success' => false, 'message' => 'A valid category ID is required.'], 422);
        if (!$db->fetchCategory($id)) respond(['success' => false, 'message' => 'Category not found.'], 404);

        try {
            $db->deleteCategory($id);
        } catch (PDOException $e) {
            respond(['success' => false, 'message' => 'Cannot delete this category while products use it. Delete or reassign those products first.'], 409);
        }
        respond(['success' => true, 'message' => 'Category deleted successfully.']);
    }

    header('Allow: GET, POST, PUT, DELETE');
    respond(['success' => false, 'message' => 'Method not allowed.'], 405);
} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(['success' => false, 'message' => 'Server error. Check PHP error log and database settings.'], 500);
}
