<?php
session_start();
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'spa_booking');
define('DB_PORT', '3306');

class DB_con
{
    private $connect;

    public function __construct()
    {
        try {
            $this->connect = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS
            );
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "<br/>";
            die();
        }
    }


    // ==================== Users ====================

    // check if an email is already registered
    public function emailExists($email)
    {
        $sql = "SELECT id FROM users WHERE email = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // create a new customer account
    public function register($full_name, $email, $phone, $password)
    {
        // never save the real password, save a hashed version
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, phone, password, role)
                VALUES (?, ?, ?, ?, 'customer')";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$full_name, $email, $phone, $hashed_password]);
    }

    // returns the user row if email + password are correct, otherwise false
    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function countCustomers()
    {
        $sql = "SELECT COUNT(*) FROM users WHERE role = 'customer'";
        return $this->connect->query($sql)->fetchColumn();
    }


    // ==================== Categories ====================

    public function fetchCategories()
    {
        $sql = "SELECT * FROM categories";
        return $this->connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchCategory($id)
    {
        $sql = "SELECT * FROM categories WHERE id = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory($name, $description, $image)
    {
        $sql = "INSERT INTO categories (name, description, image) VALUES (?, ?, ?)";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$name, $description, $image]);
    }

    public function updateCategory($id, $name, $description, $image)
    {
        $sql = "UPDATE categories
                SET name = ?, description = ?, image = ?
                WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$name, $description, $image, $id]);
    }

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM categories WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$id]);
    }

    // how many services are inside this category (we can't delete a category that still has services)
    public function countServicesInCategory($category_id)
    {
        $sql = "SELECT COUNT(*) FROM services WHERE category_id = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$category_id]);

        return $stmt->fetchColumn();
    }


    // ==================== Services ====================

    // JOIN to get the category name with each service
    public function fetchServices()
    {
        $sql = "SELECT services.*, categories.name AS category_name
                FROM services
                JOIN categories ON services.category_id = categories.id
                ORDER BY services.category_id, services.id";

        return $this->connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // only the services of one category
    public function fetchServicesByCategory($category_id)
    {
        $sql = "SELECT services.*, categories.name AS category_name
                FROM services
                JOIN categories ON services.category_id = categories.id
                WHERE services.category_id = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$category_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // a few services for the home page
    public function fetchFeaturedServices()
    {
        $sql = "SELECT services.*, categories.name AS category_name
                FROM services
                JOIN categories ON services.category_id = categories.id
                ORDER BY services.price DESC
                LIMIT 6";

        return $this->connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchService($id)
    {
        $sql = "SELECT services.*, categories.name AS category_name
                FROM services
                JOIN categories ON services.category_id = categories.id
                WHERE services.id = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createService($category_id, $name, $description, $price, $duration, $image)
    {
        $sql = "INSERT INTO services (category_id, name, description, price, duration, image)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$category_id, $name, $description, $price, $duration, $image]);
    }

    public function updateService($id, $category_id, $name, $description, $price, $duration, $image)
    {
        $sql = "UPDATE services
                SET category_id = ?, name = ?, description = ?, price = ?, duration = ?, image = ?
                WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$category_id, $name, $description, $price, $duration, $image, $id]);
    }

    public function deleteService($id)
    {
        $sql = "DELETE FROM services WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$id]);
    }

    // how many bookings use this service (we can't delete a service that has bookings)
    public function countBookingsForService($service_id)
    {
        $sql = "SELECT COUNT(*) FROM bookings WHERE service_id = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$service_id]);

        return $stmt->fetchColumn();
    }

    public function countServices()
    {
        $sql = "SELECT COUNT(*) FROM services";
        return $this->connect->query($sql)->fetchColumn();
    }


    // ==================== Bookings ====================

    // true if the same service is already booked at the same date and time
    public function isSlotTaken($service_id, $booking_date, $booking_time)
    {
        $sql = "SELECT id FROM bookings
                WHERE service_id = ? AND booking_date = ? AND booking_time = ?
                AND status != 'cancelled'";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$service_id, $booking_date, $booking_time]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }

        return false;
    }

    public function createBooking($user_id, $service_id, $booking_date, $booking_time, $notes)
    {
        $sql = "INSERT INTO bookings (user_id, service_id, booking_date, booking_time, notes)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$user_id, $service_id, $booking_date, $booking_time, $notes]);
    }

    // bookings of one customer + the service info (JOIN)
    public function fetchUserBookings($user_id)
    {
        $sql = "SELECT bookings.*, services.name AS service_name, services.price,
                       services.duration, services.image
                FROM bookings
                JOIN services ON bookings.service_id = services.id
                WHERE bookings.user_id = ?
                ORDER BY bookings.booking_date DESC, bookings.booking_time DESC";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // customer cancels his own booking (only if it is still pending or confirmed)
    public function cancelBooking($booking_id, $user_id)
    {
        $sql = "UPDATE bookings SET status = 'cancelled'
                WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$booking_id, $user_id]);
    }

    // all bookings for the admin + customer name + service name (2 JOINs)
    public function fetchAllBookings()
    {
        $sql = "SELECT bookings.*, users.full_name, users.phone,
                       services.name AS service_name, services.price
                FROM bookings
                JOIN users ON bookings.user_id = users.id
                JOIN services ON bookings.service_id = services.id
                ORDER BY bookings.booking_date DESC, bookings.booking_time DESC";

        return $this->connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBookingStatus($booking_id, $status)
    {
        $sql = "UPDATE bookings SET status = ? WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$status, $booking_id]);
    }

    public function countBookings()
    {
        $sql = "SELECT COUNT(*) FROM bookings";
        return $this->connect->query($sql)->fetchColumn();
    }

    public function countPendingBookings()
    {
        $sql = "SELECT COUNT(*) FROM bookings WHERE status = 'pending'";
        return $this->connect->query($sql)->fetchColumn();
    }


    // ==================== Image upload ====================

    // returns the saved path (uploads/xxx.jpg) or false if the image is not valid
    public function uploadImage($file)
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // max size 2MB
        if ($file['size'] > 2 * 1024 * 1024) {
            return false;
        }

        // allowed extensions only
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            return false;
        }

        // unique name so two images with the same name don't replace each other
        $path = "uploads/" . uniqid() . "." . $ext;

        if (move_uploaded_file($file['tmp_name'], $path)) {
            return $path;
        }

        return false;
    }
}
