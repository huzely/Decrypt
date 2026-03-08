<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'genzmovie';
const DB_USER = 'root';
const DB_PASS = '';
const BASE_URL = 'http://localhost/genzmovie';
const APP_NAME = 'GENZMOVIE';
const OPHIM_API_BASE = 'https://ophim1.com/v1/api';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
