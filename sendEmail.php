<?php
header('Content-Type: application/json');

// 1) Carrega .env
$dotenvFile = __DIR__.'/.env';
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name).'='.trim($value));
    }
}

// 2) Leitura das variáveis
$smtpHost   = getenv('SMTP_HOST');
$smtpPort   = getenv('SMTP_PORT');
$smtpSecure = getenv('SMTP_SECURE') === 'true' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : '';
$smtpUser   = getenv('SMTP_USER');
$smtpPass   = getenv('SMTP_PASS');
$emailFrom  = getenv('EMAIL_FROM');
$emailReply = getenv('EMAIL_REPLY_TO');
$emailAutor = getenv('EMAIL_AUTOR');
$emailContato = getenv('EMAIL_CONTATO');

// 3) Dados do formulário
$name       = $_POST['name']       ?? '';
$email      = $_POST['email']      ?? '';
$whatsapp   = $_POST['whatsapp']   ?? '';
$cpf        = $_POST['cpf']        ?? '';
$profession = $_POST['profession'] ?? '';
$product    = $_POST['product']    ?? '';
$value      = $_POST['value']      ?? '';
$datetime   = date('d/m/Y H:i:s');

require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

try {
    $mail = new PHPMailer(true);
    // SMTP
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port       = $smtpPort;

    // Remetente e resposta
    $mail->setFrom($emailFrom, 'Jubarthi LP');
    $mail->addReplyTo($emailReply);

    // Destinatários
    if (stripos($product, 'Livro') !== false) {
        $mail->addAddress($emailAutor);
    } else {
        $mail->addAddress($emailContato);
        $mail->addCC($emailAutor);
    }

    // Assunto
    $mail->Subject = "ATENDIMENTO LP [{$product}] - {$name}";

    // Corpo
    $body  = "<h2>Novo Contato na LP</h2>";
    $body .= "<p><strong>Produto/Plano:</strong> {$product}</p>";
    $body .= "<p><strong>Nome:</strong> {$name}</p>";
    $body .= "<p><strong>Email:</strong> {$email}</p>";
    $body .= "<p><strong>WhatsApp:</strong> {$whatsapp}</p>";
    $body .= "<p><strong>CPF:</strong> {$cpf}</p>";
    $body .= "<p><strong>Profissão:</strong> {$profession}</p>";
    $body .= "<p><strong>Valor:</strong> R$ {$value}</p>";
    $body .= "<p><strong>Data/Hora:</strong> {$datetime}</p>";

    $mail->isHTML(true);
    $mail->Body = $body;

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
