<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'agusfull22@hotmail.com'; // Tu correo de Hotmail
    public string $fromName   = 'Tu Nombre o Empresa'; // Nombre que verá el receptor
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = 'smtp.office365.com'; // Servidor SMTP de Hotmail

    /**
     * SMTP Username
     */
    public string $SMTPUser = 'agusfull22@hotmail.com'; // Tu correo de Hotmail

    /**
     * SMTP Password - Usa contraseña de aplicación aquí
     */
    public string $SMTPPass = 'fkthqfultsfbxlbr'; // Contraseña de aplicación

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587; // Puerto SMTP para Outlook/Hotmail

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 60;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html'; // Para poder enviar imágenes y PDF

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Newline character
     */
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
