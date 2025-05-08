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
$itemID = $_POST['editItemId'] ? trim($_POST['editItemId']) : '';
$category = $_POST['editCategory'] ? trim($_POST['editCategory']) : ''; // Corrected field names
$brand = $_POST['editItemBrand'] ? trim($_POST['editItemBrand']) : '';
$model = $_POST['editItemModel'] ? trim($_POST['editItemModel']) : '';
$itemName = $_POST['editItemName'] ? trim($_POST['editItemName']) : '';
$itemDesc = $_POST['editItemDescription'] ? trim($_POST['editItemDescription']) : ''; // Corrected field name
$reorderLevel = $_POST['editReorderLevel'] ? intval($_POST['editReorderLevel']) : 0;
$UpdatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';
$Status = 1;

// Validate required fields
if (empty($itemName) || empty($itemDesc)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Prepare to update data in the database
$sql = "UPDATE items SET Category = ?, Brand = ?, Model = ?, ItemName = ?, ItemDesc = ?, ReorderLevel = ?, UpdatedBy = ?, DateUpdated = NOW() WHERE ID = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('sssssisi', $category, $brand, $model, $itemName, $itemDesc, $reorderLevel, $UpdatedBy, $itemID);

    if ($stmt->execute()) {
        $itemId = $itemID; // Use the existing item ID

        // Check if image is uploaded
        if (isset($_FILES['ItemImageUpload']) && $_FILES['ItemImageUpload']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Pharmacy_infosys/Seller/Assets/img/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Retrieve the current image path from the database
            $currentImageQuery = "SELECT ItemPath FROM items WHERE ID = ?";
            if ($imageStmt = $conn->prepare($currentImageQuery)) {
                $imageStmt->bind_param('i', $itemID);
                $imageStmt->execute();
                $imageStmt->store_result();
                $imageStmt->bind_result($currentImagePath);
                $imageStmt->fetch();
                $imageStmt->close();

                // If there's a current image, delete it from the server
                if (!empty($currentImagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentImagePath)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $currentImagePath); // Delete old image
                }
            }

            // Process the new uploaded image
            $fileTmpPath = $_FILES['ItemImageUpload']['tmp_name'];
            $fileName = $_FILES['ItemImageUpload']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedFileExtensions = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($fileExtension, $allowedFileExtensions)) {
                http_response_code(400); // Bad Request
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
                exit;
            }

            // New file name with item ID
            $newFileName = $itemId . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $filePath_without_documentRoot = '/Pharmacy_infosys/Seller/Assets/img/products/' . $newFileName;

                // Prepare the update query to store the new image path
                $updateSql = "UPDATE items SET ItemPath = ? WHERE id = ?";
                if ($updateStmt = $conn->prepare($updateSql)) {
                    $updateStmt->bind_param('si', $filePath_without_documentRoot, $itemId);
                    if ($updateStmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully with the new image.']);
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
            echo json_encode(['status' => 'success', 'message' => 'Product updated successfully without image.']);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Database error updating product']);
    }

    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement for updating product']);
}

$conn->close();
?>
