<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; 

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email address"]);
    exit();
}

$customerEmail = $data["email"];
$order = $data["order"];
$subtotal = $data["subtotal"];
$tax = $data["tax"];
$discount = $data["discount"];
$total = $data["total"];

$emailBody = "
    <html>
    <head>
        <title>Invoice - Cash Register Pro</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; }
            h2 { margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .total-row { font-weight: bold; }
            .text-right { text-align: right; }
        </style>
    </head>
    <body>
        <h2>Cash Register Pro - Invoice</h2>
        <hr>
        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>";

foreach ($order as $item) {
    $emailBody .= "
        <tr>
            <td>{$item['name']}</td>
            <td class='text-right'>{$item['quantity']}</td>
            <td class='text-right'>".number_format($item['price'], 2)."</td>
            <td class='text-right'>".number_format($item['discount'], 2)."</td>
            <td class='text-right'>".number_format($item['tax'], 2)."</td>
            <td class='text-right'>".number_format($item['total'], 2)."</td>
        </tr>";
}

$emailBody .= "
        <tr class='total-row'>
            <td colspan='3' class='text-right'>Subtotal</td>
            <td class='text-right'>".number_format($discount, 2)."</td>
            <td class='text-right'>".number_format($tax, 2)."</td>
            <td class='text-right'>".number_format($total, 2)."</td>
        </tr>
        </table>
        <hr>
        <p>Thank you for shopping with us!</p>
    </body>
    </html>";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Use the correct SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'tajerkemer46031@gmail.com'; // Replace with your email
    $mail->Password = 'ytyx wezi ncgx uybe'; // Use the App Password....nsdu vtcm mfnm pet
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    

    $mail->setFrom('tajerkemer46031@gmail.com', 'Cash Register Pro');
    $mail->addAddress($customerEmail);
    $mail->isHTML(true);
    $mail->Subject = 'Your Invoice from Cash Register Pro';
    $mail->Body = $emailBody;

    $mail->send();
    echo json_encode(["status" => "success", "message" => "Receipt sent successfully"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
