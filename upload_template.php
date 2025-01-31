<?php
require_once './includes/config.php';
require_once './includes/function.php';
require_once 'vendor/autoload.php'; // Add this line

// Authorization check
if (!isset($_SESSION['user_id']) || !checkUserRole($_SESSION['user_id'])) {
    http_response_code(403);
    die('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['template'];

    // Validate DOCX file
    $allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!in_array($file['type'], $allowedTypes) || $file['size'] > 5242880) {
        die("Invalid DOCX file");
    }

    try {
        // Load DOCX file properly
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file['tmp_name']);
        $content = '';

        // Extract text from all sections
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $content .= $element->getText() . "\n";
                }
            }
        }

        // Capture placeholders (including array syntax) in ${...} format
        preg_match_all('/\$\{([#\/]?)([a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)*)\}/', $content, $matches);
        $placeholders = array_unique($matches[2]);

        // Create a "polished" version of the uploaded file name
        // 1. Remove extension from original name
        // 2. Replace disallowed characters with underscores
        // 3. Append a short random string for uniqueness
        $originalBaseName = pathinfo($file['name'], PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $originalBaseName);
        $randomSuffix = bin2hex(random_bytes(4));
        $filename = $sanitizedName . '_' . $randomSuffix . '.docx';

        $target_path = "uploads/templates/" . $filename;

        move_uploaded_file($file['tmp_name'], $target_path);

        activityLog($_SESSION['user_id'],'Template Upload', $filename);
        // Save to database
        $stmt = $conn->prepare("INSERT INTO document_templates 
                               (user_id, file_path, placeholders) 
                               VALUES (?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $target_path,
            json_encode($placeholders, JSON_UNESCAPED_UNICODE)
        ]);

        header("Location: ./documents.php?success=1");
        exit;

    } catch (Exception $e) {
        die("Error processing template: " . $e->getMessage());
    }
}
