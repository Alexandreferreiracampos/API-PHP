<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregar as dependências do PHPMailer
require '../../vendor/autoload.php';

// Configurações do servidor SMTP do Gmail
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'live.smtp.mailtrap.io';
$mail->SMTPAuth = true;
$mail->Username = 'api'; // Seu endereço de email do Gmail
$mail->Password = '76bc437876f01caf04a270f792cdb51c'; // Sua senha do Gmail
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Remetente e destinatário
$destinatario_email = 'alexandrerazrmaxx@gmail.com'; // Substitua pelo endereço de email do destinatário
$mail->setFrom('infocentercnp@mailtrap.club', 'Alexandre');
$mail->addAddress($destinatario_email, 'teste');

// Conteúdo do email
$mail->isHTML(true);
$mail->Subject = 'Redefinição de Senha';
$mail->Body    = 'Olá,<br><br>Você solicitou a redefinição de senha. Clique no link a seguir para redefinir sua senha: <a href="https://seusite.com/redefinir-senha.php?token=">Redefinir senha</a>.<br><br>Se você não solicitou esta redefinição, ignore este email.<br><br>Atenciosamente,<br>Sua equipe.';
$mail->AltBody = 'Olá,\n\nVocê solicitou a redefinição de senha. Clique no link a seguir para redefinir sua senha: https://seusite.com/redefinir-senha.php?token=\n\nSe você não solicitou esta redefinição, ignore este email.\n\nAtenciosamente,\nSua equipe.';

try {
    // Enviar o email
    $mail->send();
    echo 'Email de redefinição de senha enviado com sucesso.';
} catch (Exception $e) {
    echo 'Erro ao enviar o email de redefinição de senha: ' . $mail->ErrorInfo;
}
?>