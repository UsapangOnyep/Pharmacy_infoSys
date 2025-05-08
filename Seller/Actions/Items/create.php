<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Early return if not POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Ensure POST data is set
$category = $_POST['ItemCategory'] ? trim($_POST['ItemCategory']) : '';
$brand = $_POST['ItemBrand'] ? trim($_POST['ItemBrand']) : '';
$model = $_POST['ItemModel'] ? trim($_POST['ItemModel']) : '';
$itemName = $_POST['ItemName'] ? trim($_POST['ItemName']) : '';
$itemDesc = $_POST['ItemLongDesc'] ? trim($_POST['ItemLongDesc']) : ''; 
$reorderLevel = $_POST['ReorderLevel'] ? intval($_POST['ReorderLevel']) : 0;
$CreatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';
$Status = 1;

// Validate required fields
if (empty($itemName) || empty($itemDesc)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Prepare to insert data into the database
$sql = "INSERT INTO items (Category, Brand, Model, ItemName, ItemDesc, ReorderLevel, `Status`, CreatedBy, DateCreated) 
    VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW())";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('sssssis', $category, $brand, $model, $itemName, $itemDesc, $reorderLevel, $CreatedBy);

    if ($stmt->execute()) {
        $itemId = $stmt->insert_id; // Get the new item ID

        // Check if image is uploaded
        if (isset($_FILES['ItemImageUpload']) && $_FILES['ItemImageUpload']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Seller/Assets/img/products/'; // Corrected directory path
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Ensure the directory exists
            }

            $fileTmpPath = $_FILES['ItemImageUpload']['tmp_name'];
            $fileName = $_FILES['ItemImageUpload']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedFileExtensions = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($fileExtension, $allowedFileExtensions)) {
                http_response_code(400); // Bad Request
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
                exit;
            }

            // New file name with item ID and file extension
            $newFileName = $itemId . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Correct file path to be saved in the database
                $filePath_without_documentRoot = '/Seller/Assets/img/products/' . $newFileName;

                // Prepare the update query to store the image path
                $updateSql = "UPDATE items SET ItemPath = ? WHERE id = ?";
                if ($updateStmt = $conn->prepare($updateSql)) {
                    $updateStmt->bind_param('si', $filePath_without_documentRoot, $itemId);
                    if ($updateStmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Product added successfully.']);
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(['status' => 'error', 'message' => 'Database error updating image path']);
                    }
                    $updateStmt->close();
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement for updating image path']);
                }
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['status' => 'error', 'message' => 'Error moving uploaded file']);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Product added successfully without image.']);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Database error inserting product']);
    }

    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement for inserting product']);
}

$conn->close();
?>
