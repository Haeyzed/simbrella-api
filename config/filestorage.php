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
        'about_images' => config('app.name').'/'.env('FILESTORAGE_ABOUT_IMAGES_PATH', config('app.name').'/about/images'),
        'page_images' => config('app.name').'/'.env('FILESTORAGE_PAGE_IMAGES_PATH', config('app.name').'/page/images'),
        'blog_banners' => config('app.name').'/'.env('FILESTORAGE_BLOG_BANNERS_PATH', config('app.name').'/blog/banners'),
        'career_banners' => config('app.name').'/'.env('FILESTORAGE_CAREER_BANNERS_PATH', config('app.name').'/career/banners'),
        'blog_images' => config('app.name').'/'.env('FILESTORAGE_BLOG_IMAGES_PATH', config('app.name').'/blog/images'),
        'hero_images' => config('app.name').'/'.env('FILESTORAGE_HERO_IMAGES_PATH', config('app.name').'/hero/images'),
        'user_profiles' => config('app.name').'/'.env('FILESTORAGE_USER_PROFILES_PATH', config('app.name').'/user/profiles'),
        'client_logos' => config('app.name').'/'.env('FILESTORAGE_CLIENT_LOGOS_PATH', config('app.name').'/client/logos'),
        'services' => config('app.name').'/'.env('FILESTORAGE_SERVICE_IMAGES_PATH', config('app.name').'/service/images'),
    ],
];
