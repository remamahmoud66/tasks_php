<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';

try {
    $db = new DB_con();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $id = positiveId($_GET['id'] ?? null);
        if ($id > 0) {
            $user = $db->fetchUser($id);
            if (!$user) respond(['success' => false, 'message' => 'User not found.'], 404);
            respond(['success' => true, 'data' => $user]);
        }
        respond(['success' => true, 'data' => $db->fetchUsers()]);
    }

    if ($method === 'POST' || $method === 'PUT') {
        $data = requestData();
        $id = positiveId($data['id'] ?? $_GET['id'] ?? null);
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond(['success' => false, 'message' => 'A name and valid email are required.'], 422);
        }

        if ($id > 0) {
            if (!$db->fetchUser($id)) respond(['success' => false, 'message' => 'User not found.'], 404);
            $db->updateUser($id, $name, $email, $password !== '' ? $password : null);
            respond(['success' => true, 'message' => 'User updated successfully.']);
        }

        if (strlen($password) < 8) {
            respond(['success' => false, 'message' => 'Password must be at least 8 characters.'], 422);
        }

        $db->createUser($name, $email, $password);
        respond(['success' => true, 'message' => 'User created successfully.'], 201);
    }

    if ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $input);
        $id = positiveId($_GET['id'] ?? $input['id'] ?? null);
        if ($id < 1) respond(['success' => false, 'message' => 'A valid user ID is required.'], 422);
        if (!$db->fetchUser($id)) respond(['success' => false, 'message' => 'User not found.'], 404);
        $db->deleteUser($id);
        respond(['success' => true, 'message' => 'User deleted successfully.']);
    }

    header('Allow: GET, POST, PUT, DELETE');
    respond(['success' => false, 'message' => 'Method not allowed.'], 405);
} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(['success' => false, 'message' => 'Server error. Check PHP error log and database settings.'], 500);
}
