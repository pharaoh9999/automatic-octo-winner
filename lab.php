<?php
require_once 'vendor/autoload.php';
use thiagoalessio\TesseractOCR\TesseractOCR;

try {
    // Original image path
    $imagePath = 'C:/xampp/htdocs/kestrel/elements/images/front.JPG';

    // Initialize Imagick on the image
    $image = new Imagick($imagePath);

    // Convert to grayscale, adjust contrast, sharpen, and resize
    $image->modulateImage(100, 0, 100); // Grayscale
    $image->contrastImage(1);
    $image->adaptiveSharpenImage(2, 1);
    $image->resizeImage(2000, 0, Imagick::FILTER_LANCZOS, 1);

    // Perform OCR on the processed image without saving it
    $ocr = new TesseractOCR();
    $ocr->imageData($image->getImageBlob(), $image->getImageLength())
        ->lang('eng')
        ->psm(6);
    $text = $ocr->run();

    // Display raw OCR text to check if anything is captured
    echo "OCR Raw Text Output:\n";
    echo $text;

    // Adjusted function to parse the extracted text into an array
    function parseIdDetails($text) {
        $details = [];
    
        // Updated regular expressions to handle symbols and artifacts
        if (preg_match('/SERIAL NUMBER:\s*([0-9]+[:;]?[0-9]*)/i', $text, $matches)) {
            // Extract only digits if there’s an extra colon or semicolon
            $details['serial_number'] = preg_replace('/[^0-9]/', '', $matches[1]);
        }
        if (preg_match('/JDAUMBEEFE?\s*([0-9]+)/i', $text, $matches)) {  // Trying to capture ID Number despite artifacts
            $details['id_number'] = preg_replace('/[^0-9]/', '', $matches[1]);
        }
        if (preg_match('/FULL NAMES\s*[:~\'"]*\s*([A-Z\s\'\-]+)/i', $text, $matches)) {
            $details['full_names'] = trim($matches[1]);
        }
        if (preg_match('/DATE OF BIRTH\s*[:~\'-]*\s*([0-9.\/-]+)/i', $text, $matches)) {
            $details['date_of_birth'] = $matches[1];
        }
        if (preg_match('/SEX\s*[:~\'-]*\s*(MALE|FEMALE)/i', $text, $matches)) {
            $details['sex'] = ucfirst(strtolower($matches[1]));
        }
        if (preg_match('/DISTRICT OF BIRTH\s*[:~\'-]*\s*([A-Z\s]+)/i', $text, $matches)) {
            $details['district_of_birth'] = trim($matches[1]);
        }
        if (preg_match('/PLACE OF ISSUE\s*[:~\'-]*\s*([A-Z\s]+)/i', $text, $matches)) {
            $details['place_of_issue'] = trim($matches[1]);
        }
        if (preg_match('/DATE OF ISSUE\s*[:~\'-]*\s*([0-9.\/-]+)/i', $text, $matches)) {
            $details['date_of_issue'] = $matches[1];
        }
    
        return $details;
    }
    

    // Parse the OCR text and organize it in an array
    $idDetails = parseIdDetails($text);

    // Output the array
    print_r($idDetails);

} catch (ImagickException $e) {
    echo 'Imagick Exception: ', $e->getMessage();
} catch (Exception $e) {
    echo 'General Exception: ', $e->getMessage();
}
