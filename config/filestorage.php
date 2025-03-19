<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Driver
    |--------------------------------------------------------------------------
    |
    | This value determines which of the following storage driver to use
    | as your default driver for all file storage operations. The available
    | drivers are: "local", "aws", "cloudinary", "dropbox", "google"
    |
    */
    'default' => env('FILESTORAGE_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Storage Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage disks for your application.
    |
    */
    'disks' => [
        'local' => [
            'disk' => env('FILESTORAGE_LOCAL_DISK', 'public'),
        ],

        'aws' => [
            'disk' => env('FILESTORAGE_AWS_DISK', 's3'),
        ],

        'cloudinary' => [
            'disk' => env('FILESTORAGE_CLOUDINARY_DISK', 'cloudinary'),
        ],

        'dropbox' => [
            'disk' => env('FILESTORAGE_DROPBOX_DISK', 'dropbox'),
        ],

        'google' => [
            'disk' => env('FILESTORAGE_GOOGLE_DISK', 'gcs'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Paths
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage paths for your application.
    |
    */
    'paths' => [
        'about_images' => env('FILESTORAGE_ABOUT_IMAGES_PATH', 'about/images'),
        'blog_banners' => env('FILESTORAGE_BLOG_BANNERS_PATH', 'blog/banners'),
        'career_banners' => env('FILESTORAGE_CAREER_BANNERS_PATH', 'career/banners'),
        'blog_images' => env('FILESTORAGE_BLOG_IMAGES_PATH', 'blog/images'),
        'hero_images' => env('FILESTORAGE_HERO_IMAGES_PATH', 'hero/images'),
        'user_profiles' => env('FILESTORAGE_USER_PROFILES_PATH', 'user/profiles'),
        'client_logos' => env('FILESTORAGE_CLIENT_LOGOS_PATH', 'client/logos'),
        'services' => env('FILESTORAGE_SERVICE_IMAGES_PATH', 'service/images'),
    ],
];
