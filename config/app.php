<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Kolkata');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const APP_NAME = 'Smart City Tiruppur';
const APP_ENV = 'local'; // Change to 'production' on the live server.
const BASE_URL = '/test/smartcitytiruppur'; // Change if your project folder or domain path is different.

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'smartcitytiruppur';
const DB_USER = 'root';
const DB_PASS = '';

const OTP_TTL_SECONDS = 300;
const OTP_RESEND_SECONDS = 60;
const MAX_UPLOAD_BYTES = 2097152; // 2 MB
const MAX_ATTACHMENTS = 4;
const REOPEN_DAYS = 7;
