<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration options for email notifications
    | in the application. You can configure whether to send various types
    | of notifications and customize their behavior.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | User Registration Notifications
    |--------------------------------------------------------------------------
    |
    | These settings control notifications sent when new users register.
    |
    */

    'send_welcome_email' => env('MAIL_SEND_WELCOME_EMAIL', true),
    'notify_admins_new_user' => env('MAIL_NOTIFY_ADMINS_NEW_USER', true),

    /*
    |--------------------------------------------------------------------------
    | Default Email Settings
    |--------------------------------------------------------------------------
    |
    | Configure default email addresses and behaviors.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    |
    | Control whether emails should be queued or sent immediately.
    |
    */

    'queue_emails' => env('MAIL_QUEUE_EMAILS', true),
    'queue_connection' => env('MAIL_QUEUE_CONNECTION', 'default'),

];