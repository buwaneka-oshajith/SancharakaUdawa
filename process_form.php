<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

require 'tcpdf/tcpdf.php'; // Path to your TCPDF library

// Function to sanitize form inputs
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $companyName = sanitizeInput($_POST['companyName']);
    $companyAddress = sanitizeInput($_POST['companyAddress']);
    $telephone = sanitizeInput($_POST['telephone']);
    $mobile = sanitizeInput($_POST['mobile']);
    $contactPerson = sanitizeInput($_POST['contactPerson']);
    $contactPersonPosition = sanitizeInput($_POST['contactPersonPosition']);
    $membershipType = sanitizeInput($_POST['membershipType']);
    $companySize = sanitizeInput($_POST['companySize']);
    $date = sanitizeInput($_POST['date']);
    $position = sanitizeInput($_POST['position']);
    $units = sanitizeInput($_POST['units']);
    $sqm = sanitizeInput($_POST['sqm']);
    $cost = sanitizeInput($_POST['cost']);
    $opt1 = sanitizeInput($_POST['opt1']);
    $opt2 = sanitizeInput($_POST['opt2']);
    $tcost = sanitizeInput($_POST['tcost']);

     // Validate and sanitize email
     $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
     if (!$email) {
         // Handle invalid email address
         echo 'Invalid email address';
         exit;
     }

    // Validate other inputs based on your specific requirements

    // Handle file uploads for signature
    $signatureFilename = $_FILES['signature']['name'];
    $signatureTmpName = $_FILES['signature']['tmp_name'];

    move_uploaded_file($signatureTmpName, $signatureFilename);

    // Handle file uploads for seal
    $sealFilename = $_FILES['seal']['name'];
    $sealTmpName = $_FILES['seal']['tmp_name'];

    move_uploaded_file($sealTmpName, $sealFilename); 

    // Create PDF using TCPDF
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('aealarabiya', '', 12);
    $pdf->Cell(0, 10, 'Membership Form', 0, 1, 'C');

    
    // Add more content to the PDF based on your form fields
    // Add form data to PDF
$pdf->Cell(0, 10, "Company Name: $companyName", 0, 1, 'L');
$pdf->Cell(0, 10, "Company Address: $companyAddress", 0, 1, 'L');
$pdf->Cell(0, 10, "Telephone: $telephone", 0, 1, 'L');
$pdf->Cell(0, 10, "Mobile: $mobile", 0, 1, 'L');
$pdf->Cell(0, 10, "Contact Person: $contactPerson", 0, 1, 'L');
$pdf->Cell(0, 10, "Position of Contact Person: $contactPersonPosition", 0, 1, 'L');
$pdf->Cell(0, 10, "Membership Type: $membershipType", 0, 1, 'L');
$pdf->Cell(0, 10, "Company Size: $companySize", 0, 1, 'L');
$pdf->Cell(0, 10, "Date: $date", 0, 1, 'L');
$pdf->Cell(0, 10, "Position: $position", 0, 1, 'L');
$pdf->Cell(0, 10, "Units: $units", 0, 1, 'L');
$pdf->Cell(0, 10, "SQM: $sqm", 0, 1, 'L');
$pdf->Cell(0, 10, "Cost: $cost", 0, 1, 'L');
$pdf->Cell(0, 10, "Option 1: $opt1", 0, 1, 'L');
$pdf->Cell(0, 10, "Option 2: $opt2", 0, 1, 'L');
$pdf->Cell(0, 10, "Total Cost: $tcost", 0, 1, 'L');



   // Add signature image to PDF
$signatureImage = __DIR__ . '/' . $signatureFilename;

// Determine the image format dynamically
$imageInfo = getimagesize($signatureImage);
$mime = $imageInfo['mime'];

// Use the appropriate Image method based on the image format
if ($mime === 'image/jpeg') {
    $pdf->Image($signatureImage, 10, $pdf->GetY() + 10, 40, 20, 'JPEG');
} elseif ($mime === 'image/png') {
    $pdf->Image($signatureImage, 10, $pdf->GetY() + 10, 40, 20, 'PNG');
} else {
    // Handle unsupported image format
    die("Unsupported image format: $mime");
}

// Add text below the signature image
$pdf->SetY($pdf->GetY() + 15); // Adjust the Y position as needed
$pdf->Cell(0, 10, 'Signature', 0, 1, 'C');



// Add seal image to PDF
$sealImage = __DIR__ . '/' . $sealFilename;
// Determine the image format dynamically
$imageInfo = getimagesize($sealImage);
$mime = $imageInfo['mime'];
// Use the appropriate Image method based on the image format
if ($mime === 'image/jpeg') {
    $pdf->Image($sealImage, 10, $pdf->GetY() + 30, 40, 20, 'JPEG');
} elseif ($mime === 'image/png') {
    $pdf->Image($sealImage, 10, $pdf->GetY() + 30, 40, 20, 'PNG');
} else {
    // Handle unsupported image format
    die("Unsupported image format: $mime");
}

// Add text below the seal image
$pdf->SetY($pdf->GetY() + 15); // Adjust the Y position as needed
$pdf->Cell(0, 10, 'Seal', 0, 1, 'C');


// Error Handling File path for images

if (!file_exists($signatureImage)) {
    die("Signature image not found: $signatureImage");
}

if (!file_exists($sealImage)) {
    die("Seal image not found: $sealImage");
}


  // Save the PDF to a file in the current directory
  $pdfPath = __DIR__ . '/directory/Membership_Form_' . date('Ymd_His') . '.pdf';
  $pdf->Output($pdfPath, 'F');

    // Send email with PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        $mail->isSMTP();
        // Set your SMTP configuration here
        $mail->Host ='mail.aitech.lk';
        $mail->SMTPAuth = true;
        $mail->Username ='gayanc@aitech.lk';
        $mail->Password ='gayan@1234';
        $mail->SMTPSecure ='tls';
        $mail->Port =587;

        // Recipients
        $mail->setFrom('buwanekav@aitech.lk', 'buwanekav');
        $mail->addAddress($_POST['email']);
        $mail->addAddress('gayanc@aitech.lk'); // Add New recipient

        // Attach PDF file
        $mail->addAttachment($pdfPath);

        // Attach signature file
        $mail->addAttachment($signatureFilename, 'Signature.jpg');

        // Attach seal file
        $mail->addAttachment($sealFilename, 'Seal.jpg');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Membership Form Submission';
        $mail->Body    = 'Thank you for submitting the membership form.';

        $mail->send();

        // Remove the temporary uploaded files
        unlink($signatureFilename);
        unlink($sealFilename);

        echo 'Message has been sent';
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
?>
