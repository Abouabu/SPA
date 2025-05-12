<?php
session_start();
require 'config.php';

// Error reporting (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Configure upload directory
$uploadDir = __DIR__ . '/images/';
$uploadUrl = 'images/';

// Create upload directory with proper permissions
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        die("Failed to create upload directory: " . htmlspecialchars($uploadDir));
    }
}

// Verify directory permissions
if (!is_writable($uploadDir)) {
    die("Upload directory is not writable: " . htmlspecialchars($uploadDir));
}

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_service'])) {
        try {
            $pdo->beginTransaction();

            // Process main service data
            $id = (int)($_POST['service_id'] ?? 0);
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $image_url = null;

            // Get existing image if editing
            if ($id > 0) {
                $stmt = $pdo->prepare("SELECT image_url FROM services WHERE id = ?");
                $stmt->execute([$id]);
                $image_url = $stmt->fetchColumn();
            }

            // Handle file upload
            if (!empty($_FILES['image']['name'])) {
                $file = $_FILES['image'];

                // Validate upload
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("File upload error: {$file['error']}");
                }

                // Validate file type
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png'
                ];

                if (!array_key_exists($mime, $allowed)) {
                    throw new Exception("Invalid file type. Allowed: JPG, PNG");
                }

                // Validate size
                $maxSize = 2 * 1024 * 1024; // 2MB
                if ($file['size'] > $maxSize) {
                    throw new Exception("File size exceeds 2MB limit");
                }

                // Generate safe filename
                $extension = $allowed[$mime];
                $baseName = preg_replace('/[^a-z0-9]/i', '_', $name);
                $fileName = sprintf('%s_%d.%s', $baseName, time(), $extension);
                $targetPath = $uploadDir . $fileName;

                // Move uploaded file
                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    throw new Exception("Failed to move uploaded file. Check permissions.");
                }

                // Delete old image if exists
                if ($id > 0 && $image_url && file_exists($image_url)) {
                    @unlink($image_url);
                }

                $image_url = $targetPath;
            }

            // Save to database
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE services SET 
                    name = ?, description = ?, image_url = ?
                    WHERE id = ?");
                $stmt->execute([$name, $description, $image_url, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO services 
                    (name, description, image_url) VALUES (?, ?, ?)");
                $stmt->execute([$name, $description, $image_url]);
                $id = $pdo->lastInsertId();
            }

            // Handle sub-services
            $subServices = json_decode($_POST['sub_services'] ?? '[]', true);
            $existingSubs = $pdo->prepare("SELECT id FROM sub_services WHERE service_id = ?");
            $existingSubs->execute([$id]);
            $currentIds = array_column($existingSubs->fetchAll(PDO::FETCH_ASSOC), 'id');

            $submittedIds = [];
            foreach ($subServices as $sub) {
                if (empty($sub['name'])) continue;

                $subId = (int)($sub['id'] ?? 0);
                $submittedIds[] = $subId;

                if ($subId > 0) {
                    $stmt = $pdo->prepare("UPDATE sub_services SET 
                        name = ?, price = ?
                        WHERE id = ?");
                    $stmt->execute([$sub['name'], $sub['price'], $subId]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO sub_services 
                        (service_id, name, price) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $sub['name'], $sub['price']]);
                }
            }

            // Delete removed sub-services
            $toDelete = array_diff($currentIds, $submittedIds);
            if (!empty($toDelete)) {
                $placeholders = implode(',', array_fill(0, count($toDelete), '?'));
                $stmt = $pdo->prepare("DELETE FROM sub_services WHERE id IN ($placeholders)");
                $stmt->execute(array_values($toDelete));
            }

            $pdo->commit();
            $success = 'Service saved successfully!';
            header("Location: manage_services.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

// Handle service deletion
if (isset($_GET['delete'])) {
    try {
        $service_id = (int)$_GET['delete'];

        $pdo->beginTransaction();

        // Get image path
        $stmt = $pdo->prepare("SELECT image_url FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $image_url = $stmt->fetchColumn();

        // Delete sub-services
        $stmt = $pdo->prepare("DELETE FROM sub_services WHERE service_id = ?");
        $stmt->execute([$service_id]);

        // Delete service
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$service_id]);

        // Delete image file
        if ($image_url && file_exists($image_url)) {
            @unlink($image_url);
        }

        $pdo->commit();
        $success = 'Service deleted successfully!';
        header("Location: manage_services.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Error deleting service: ' . $e->getMessage();
    }
}

// Fetch services with sub-services
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
                if (!empty($sub)) {
                    list($id, $name, $price) = explode('::', $sub);
                    $service['sub_services'][] = [
                        'id' => $id,
                        'name' => $name,
                        'price' => $price
                    ];
                }
            }
        }
    }
    unset($service);
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
            max-height: 200px;
            border-radius: 8px;
        }

        .upload-preview {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .upload-preview:hover {
            border-color: #0d6efd;
        }

        .permission-note {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .sub-service-item {
            transition: all 0.3s;
        }

        .sub-service-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'admin_sidebar.php'; ?>

            <main class="main-content col-12 col-md-9 col-lg-10">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h1 class="h2 mb-3 mb-md-0">Manage Services</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="clearForm()">
                        <i class="fas fa-plus-circle me-2"></i>Add Service
                    </button>
                </div>

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
                                                <?php else: ?>
                                                    <span class="text-muted">No sub-services</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($service['image_url']): ?>
                                                    <img src="<?= htmlspecialchars(str_replace(__DIR__, '', $service['image_url'])) ?>"
                                                        class="image-preview img-thumbnail"
                                                        alt="<?= htmlspecialchars($service['name']) ?>">
                                                <?php else: ?>
                                                    <span class="text-muted">No image</span>
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
                                                    onclick="return confirm('Delete this service and all its sub-services?')">
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
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" id="serviceForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceModalLabel">Add New Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="service_id" id="serviceId">
                        <input type="hidden" name="sub_services" id="subServicesData">

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Service Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Service Image</label>
                                <div class="file-upload">
                                    <div class="upload-preview" onclick="document.getElementById('imageInput').click()">
                                        <div id="imagePreviewContainer">
                                            <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                            <p class="mb-0">Click to upload service image</p>
                                            <small class="text-muted">(Max 2MB, JPG/PNG)</small>
                                        </div>
                                        <img id="imagePreview" class="image-preview mt-2 d-none">
                                        <div id="existingImageNotice" class="text-success small mt-2 d-none">
                                            <i class="fas fa-info-circle"></i> Existing image will be kept
                                        </div>
                                    </div>
                                    <input type="file" name="image" id="imageInput"
                                        class="form-control d-none" accept="image/jpeg, image/png"
                                        onchange="validateImage(this)">
                                    <div class="permission-note">
                                        Server write permissions required for: <?= htmlspecialchars($uploadDir) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Sub-Services</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addSubService()">
                                    <i class="fas fa-plus me-2"></i>Add Sub-Service
                                </button>
                            </div>
                            <div id="subServicesContainer" class="list-group mb-3"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="save_service" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sub-Service Template -->
    <template id="subServiceTemplate">
        <div class="list-group-item sub-service-item py-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="sub_name" class="form-control" placeholder="Sub-service name" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="sub_price" class="form-control" placeholder="Price" min="0" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <input type="hidden" name="sub_id" value="0">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeSubService(this)">
                        <i class="fas fa-trash-alt me-1"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- JavaScript Section -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sub-service management
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

        // Image handling
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    container.classList.add('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Edit service handler
        function editService(service) {
            document.getElementById('serviceId').value = service.id;
            document.querySelector('input[name="name"]').value = service.name;
            document.querySelector('textarea[name="description"]').value = service.description;
            document.getElementById('serviceModalLabel').textContent = `Edit: ${service.name}`;

            // Handle image preview
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            const notice = document.getElementById('existingImageNotice');
            if (service.image_url) {
                preview.src = service.image_url.replace(/^.*\/\/[^\/]+/, '');
                preview.classList.remove('d-none');
                container.classList.add('d-none');
                notice.classList.remove('d-none');
            }

            // Handle sub-services
            document.getElementById('subServicesContainer').innerHTML = '';
            service.sub_services.forEach(sub => {
                addSubService({
                    id: sub.id,
                    name: sub.name,
                    price: sub.price
                });
            });
        }

        // Form handling
        document.getElementById('serviceForm').addEventListener('submit', function(e) {
            const subs = [];
            document.querySelectorAll('.sub-service-item').forEach(item => {
                subs.push({
                    id: parseInt(item.querySelector('input[name="sub_id"]').value) || 0,
                    name: item.querySelector('input[name="sub_name"]').value.trim(),
                    price: parseFloat(item.querySelector('input[name="sub_price"]').value) || 0
                });
            });
            document.getElementById('subServicesData').value = JSON.stringify(subs);
        });

        // Clear form for new entries
        function clearForm() {
            document.getElementById('serviceForm').reset();
            document.getElementById('subServicesContainer').innerHTML = '';
            document.getElementById('imagePreview').classList.add('d-none');
            document.getElementById('imagePreviewContainer').classList.remove('d-none');
            document.getElementById('existingImageNotice').classList.add('d-none');
            document.getElementById('serviceModalLabel').textContent = 'Add New Service';
        }

        // Image validation
        function validateImage(input) {
            const file = input.files[0];
            if (!file) return;

            const validTypes = ['image/jpeg', 'image/png'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(file.type)) {
                alert('Only JPG/PNG images allowed!');
                input.value = '';
                return;
            }

            if (file.size > maxSize) {
                alert('File size exceeds 2MB limit!');
                input.value = '';
                return;
            }

            previewImage(input);
        }
    </script>
</body>

</html>