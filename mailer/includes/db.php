<?php
/**
 * Database Connection
 * 
 * Configure your database credentials here.
 * If you don't need a database, you can skip this file.
 */

function getDBConnection(): PDO
{
    $host = '127.0.0.1';
    $db   = 'mailer_db';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Log the error but display a friendly message
        error_log('Database Connection Error: ' . $e->getMessage());
        die('Database connection error. Please check your database configuration.');
    }
}

/**
 * Get the database table prefix
 */
function getTablePrefix(): string
{
    return '';
}