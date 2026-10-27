<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // Ganti dengan alamat Gmail pengirim asli Anda
    public string $fromEmail  = 'rizkybaruna411000@gmail.com';
    public string $fromName   = 'Checksheet Management System';
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';

    // Ubah default dari 'mail' menjadi 'smtp'
    public string $protocol = 'smtp';

    public string $mailPath = '/usr/sbin/sendmail';

    // Konfigurasi SMTP Gmail
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPUser = 'rizkybaruna411000@gmail.com';
    public string $SMTPPass = 'osqlodevmaghthms'; // 16 digit App Password Gmail (tanpa spasi)
    public int $SMTPPort    = 587;
    public int $SMTPTimeout = 30;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls';

    public bool $wordWrap = true;
    public int $wrapChars = 76;

    // Ubah default dari 'text' menjadi 'html'
    public string $mailType = 'html';

    public string $charset = 'UTF-8';
    public bool $validate = false;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();

        // Otomatis baca dari .env jika tersedia
        $this->fromEmail  = env('email.fromEmail', $this->fromEmail);
        $this->fromName   = env('email.fromName', $this->fromName);
        $this->protocol   = env('email.protocol', $this->protocol);
        $this->SMTPHost   = env('email.SMTPHost', $this->SMTPHost);
        $this->SMTPUser   = env('email.SMTPUser', $this->SMTPUser);
        $this->SMTPPass   = str_replace(' ', '', (string) env('email.SMTPPass', $this->SMTPPass));
        $this->SMTPPort   = (int) env('email.SMTPPort', $this->SMTPPort);
        $this->SMTPCrypto = env('email.SMTPCrypto', $this->SMTPCrypto);
        $this->mailType   = env('email.mailType', $this->mailType);
    }
}