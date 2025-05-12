<?php
session_start();
require 'config.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Handle Service CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add/Update Service
    if (isset($_POST['save_service'])) {
        $id = $_POST['service_id'] ?? 0;
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $image_url = '';
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = 'images/';
            $fileName = basename($_FILES['image']['name']);
            $targetFile = $uploadDir . time() . '_' . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image_url = $targetFile;
            } else {
                throw new Exception('Failed to upload image.');
            }
        }


        try {
            $pdo->beginTransaction();

            if ($id > 0) {
                // Update service
                $stmt = $pdo->prepare("UPDATE services SET 
                                    name = ?, 
                                    description = ?, 
                                    image_url = ?
                                    WHERE id = ?");
                $stmt->execute([$name, $description, $image_url, $id]);
            } else {
                // Create new service
                $stmt = $pdo->prepare("INSERT INTO services (name, description, image_url) 
                                      VALUES (?, ?, ?)");
                $stmt->execute([$name, $description, $image_url]);
                $id = $pdo->lastInsertId();
            }

            // Handle sub-services
            if (!empty($_POST['sub_services'])) {
                $subServices = json_decode($_POST['sub_services'], true);

                foreach ($subServices as $sub) {
                    if (!empty($sub['name'])) {
                        if ($sub['id'] > 0) {
                            // Update existing sub-service
                            $stmt = $pdo->prepare("UPDATE sub_services SET 
                                                 name = ?, 
                                                 price = ?
                                                 WHERE id = ?");
                            $stmt->execute([$sub['name'], $sub['price'], $sub['id']]);
                        } else {
                            // Create new sub-service
                            $stmt = $pdo->prepare("INSERT INTO sub_services (service_id, name, price) 
                                                  VALUES (?, ?, ?)");
                            $stmt->execute([$id, $sub['name'], $sub['price']]);
                        }
                    }
                }
            }

            $pdo->commit();
            $success = 'Service saved successfully!';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Error saving service: ' . $e->getMessage();
        }
    }
}

// Delete Service
if (isset($_GET['delete'])) {
    $service_id = (int)$_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $success = 'Service deleted successfully!';
    } catch (PDOException $e) {
        $error = 'Error deleting service: ' . $e->getMessage();
    }
}

// Fetch all services with their sub-services
try {
    $services = $pdo->query("
        SELECT s.*, 
               GROUP_CONCAT(ss.id, '::', ss.name, '::', ss.price ORDER BY ss.id SEPARATOR '||') AS sub_services
        FROM services s
        LEFT JOIN sub_services ss ON s.id = ss.service_id
        GROUP BY s.id
        ORDER BY s.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Parse sub-services
    foreach ($services as &$service) {
        $service['sub_services'] = [];
        if (!empty($service['sub_services'])) {
            $subs = explode('||', $service['sub_services']);
            foreach ($subs as $sub) {
                list($id, $name, $price) = explode('::', $sub);
                $service['sub_services'][] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price
                ];
            }
        }
    }
} catch (PDOException $e) {
    die("Error fetching services: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: #343a40;
            color: white;
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }

        .image-preview {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .upload-preview {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .upload-preview:hover {
            border-color: #0d6efd;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'admin_sidebar.php'; ?>

            <main class="main-content col-12 col-md-9 col-lg-10">
                <!-- Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h1 class="h2 mb-3 mb-md-0">Manage Services</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal">
                        <i class="fas fa-plus-circle me-2"></i>Add Service
                    </button>
                </div>

                <!-- Alerts -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Services Table -->
                <div class="card shadow-sm">
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Description</th>
                                        <th>Sub-Services</th>
                                        <th>Image</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($services as $service): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($service['name']) ?></td>
                                            <td class="text-muted"><?= htmlspecialchars($service['description']) ?></td>
                                            <td>
                                                <?php if (!empty($service['sub_services'])): ?>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php foreach ($service['sub_services'] as $sub): ?>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                                <?= htmlspecialchars($sub['name']) ?> - KES <?= number_format($sub['price'], 2) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($service['image_url']): ?>
                                                    <img src="<?= htmlspecialchars($service['image_url']) ?>"
                                                        class="image-preview img-thumbnail"
                                                        alt="<?= htmlspecialchars($service['name']) ?>">
                                                <?php endif; ?>


                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#serviceModal"
                                                    onclick="editService(<?= htmlspecialchars(json_encode($service), ENT_QUOTES) ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?delete=<?= $service['id'] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this service?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Service Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Service Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="service_id" id="serviceId">

                        <!-- Service Details -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Service Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <div class="file-upload">
                                    <div class="upload-preview" onclick="document.getElementById('imageInput').click()">
                                        <div id="imagePreviewContainer">
                                            <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                            <p class="mb-0">Click to upload service image</p>
                                            <small class="text-muted">(Max 2MB, JPG/PNG)</small>
                                        </div>
                                        <img id="imagePreview" class="img-fluid mt-2 d-none">
                                    </div>
                                    <input type="file" name="image" id="imageInput"
                                        class="form-control d-none" accept="image/*"
                                        onchange="previewImage(this)">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Sub-Services -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Sub-Services</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addSubService()">
                                    <i class="fas fa-plus me-2"></i>Add Sub-Service
                                </button>
                            </div>

                            <div id="subServicesContainer" class="list-group mb-3">
                                <!-- Dynamic sub-services -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="save_service" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>

                    <input type="hidden" name="sub_services" id="subServicesData">

                    <script>
                        document.querySelector('form').addEventListener('submit', function() {
                            const subs = [];
                            document.querySelectorAll('.sub-service-item').forEach(item => {
                                subs.push({
                                    id: parseInt(item.querySelector('input[name="sub_id"]').value || 0),
                                    name: item.querySelector('input[name="sub_name"]').value,
                                    price: parseFloat(item.querySelector('input[name="sub_price"]').value || 0)
                                });
                            });

                            document.getElementById('subServicesData').value = JSON.stringify(subs);
                        });
                    </script>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image Preview
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    container.classList.add('d-none');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Edit Service
        function editService(service) {
            document.getElementById('serviceId').value = service.id;
            document.querySelector('input[name="name"]').value = service.name;
            document.querySelector('textarea[name="description"]').value = service.description;

            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            if (service.image_path) {
                preview.src = service.image_path;
                preview.classList.remove('d-none');
                container.classList.add('d-none');
            }

            // Sub-services handling
            document.getElementById('subServicesContainer').innerHTML = '';
            service.sub_services.forEach(sub => {
                addSubService({
                    id: sub.id,
                    name: sub.name,
                    price: sub.price
                });
            });
        }

        // Sub-Service Management
        function addSubService(data = {
            id: 0,
            name: '',
            price: ''
        }) {
            const template = document.getElementById('subServiceTemplate');
            const clone = template.content.cloneNode(true);

            clone.querySelector('input[name="sub_name"]').value = data.name;
            clone.querySelector('input[name="sub_price"]').value = data.price;
            clone.querySelector('input[name="sub_id"]').value = data.id;

            document.getElementById('subServicesContainer').appendChild(clone);
        }

        function removeSubService(btn) {
            btn.closest('.sub-service-item').remove();
        }
    </script>
</body>

</html>