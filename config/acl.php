<?php

return [
    'roles' => [
        'sadmin' => [
            'name' => 'super-admin',
            'display_name' => 'Super Admin',
            'description' => 'Super Administrator with all permissions',
        ],
        'admin' => [
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator with most permissions',
        ],
        'manager' => [
            'name' => 'manager',
            'display_name' => 'Manager',
            'description' => 'Manager with limited permissions',
        ],
        'user' => [
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Regular user with basic permissions',
        ],
    ],
    'permissions' => [
        // User permissions
        'user_view' => [
            'name' => 'user_view',
            'display_name' => 'View Users',
            'description' => 'Can view users',
            'is_system' => true,
        ],
        'user_create' => [
            'name' => 'user_create',
            'display_name' => 'Create Users',
            'description' => 'Can create new users',
            'is_system' => true,
        ],
        'user_update' => [
            'name' => 'user_update',
            'display_name' => 'Update Users',
            'description' => 'Can update existing users',
            'is_system' => true,
        ],
        'user_delete' => [
            'name' => 'user_delete',
            'display_name' => 'Delete Users',
            'description' => 'Can delete users',
            'is_system' => true,
        ],
        'user_restore' => [
            'name' => 'user_restore',
            'display_name' => 'Restore Users',
            'description' => 'Can restore deleted users',
            'is_system' => true,
        ],

        // Role permissions
        'assign_role' => [
            'name' => 'assign_role',
            'display_name' => 'Assign Roles',
            'description' => 'Can assign roles to users',
            'is_system' => true,
        ],
        'remove_role' => [
            'name' => 'remove_role',
            'display_name' => 'Remove Roles',
            'description' => 'Can remove roles from users',
            'is_system' => true,
        ],

        // Permission permissions
        'give_permission' => [
            'name' => 'give_permission',
            'display_name' => 'Give Permissions',
            'description' => 'Can give permissions to users',
            'is_system' => true,
        ],
        'revoke_permission' => [
            'name' => 'revoke_permission',
            'display_name' => 'Revoke Permissions',
            'description' => 'Can revoke permissions from users',
            'is_system' => true,
        ],
        'change_password' => [
            'name' => 'change_password',
            'display_name' => 'Change Password',
            'description' => 'Can change user passwords',
            'is_system' => true,
        ],
        'change_status' => [
            'name' => 'change_status',
            'display_name' => 'Change Status',
            'description' => 'Can change user status',
            'is_system' => true,
        ],
        'blog_view' => [
            'name' => 'blog_view',
            'display_name' => 'View Blog Posts',
            'description' => 'Can view blog posts',
            'is_system' => true,
        ],
        'blog_create' => [
            'name' => 'blog_create',
            'display_name' => 'Create Blog Posts',
            'description' => 'Can create new blog posts',
            'is_system' => true,
        ],
        'blog_update' => [
            'name' => 'blog_update',
            'display_name' => 'Update Blog Posts',
            'description' => 'Can update existing blog posts',
            'is_system' => true,
        ],
        'blog_delete' => [
            'name' => 'blog_delete',
            'display_name' => 'Delete Blog Posts',
            'description' => 'Can delete blog posts',
            'is_system' => true,
        ],
        'blog_restore' => [
            'name' => 'blog_restore',
            'display_name' => 'Restore Blog Posts',
            'description' => 'Can restore deleted blog posts',
            'is_system' => true,
        ],

        // Role permissions
        'role_view' => [
            'name' => 'role_view',
            'display_name' => 'View Roles',
            'description' => 'Can view roles',
            'is_system' => true,
        ],
        'role_create' => [
            'name' => 'role_create',
            'display_name' => 'Create Roles',
            'description' => 'Can create new roles',
            'is_system' => true,
        ],
        'role_update' => [
            'name' => 'role_update',
            'display_name' => 'Update Roles',
            'description' => 'Can update existing roles',
            'is_system' => true,
        ],
        'role_delete' => [
            'name' => 'role_delete',
            'display_name' => 'Delete Roles',
            'description' => 'Can delete roles',
            'is_system' => true,
        ],

        // Permission permissions
        'permission_view' => [
            'name' => 'permission_view',
            'display_name' => 'View Permissions',
            'description' => 'Can view permissions',
            'is_system' => true,
        ],
        'permission_create' => [
            'name' => 'permission_create',
            'display_name' => 'Create Permissions',
            'description' => 'Can create new permissions',
            'is_system' => true,
        ],
        'permission_update' => [
            'name' => 'permission_update',
            'display_name' => 'Update Permissions',
            'description' => 'Can update existing permissions',
            'is_system' => true,
        ],
        'permission_delete' => [
            'name' => 'permission_delete',
            'display_name' => 'Delete Permissions',
            'description' => 'Can delete permissions',
            'is_system' => true,
        ],
        // Career permissions
        'career_view' => [
            'name' => 'career_view',
            'display_name' => 'View Careers',
            'description' => 'Can view career opportunities',
            'is_system' => true,
        ],
        'career_create' => [
            'name' => 'career_create',
            'display_name' => 'Create Careers',
            'description' => 'Can create new career opportunities',
            'is_system' => true,
        ],
        'career_update' => [
            'name' => 'career_update',
            'display_name' => 'Update Careers',
            'description' => 'Can update existing career opportunities',
            'is_system' => true,
        ],
        'career_delete' => [
            'name' => 'career_delete',
            'display_name' => 'Delete Careers',
            'description' => 'Can delete career opportunities',
            'is_system' => true,
        ],
        'career_restore' => [
            'name' => 'career_restore',
            'display_name' => 'Restore Careers',
            'description' => 'Can restore deleted career opportunities',
            'is_system' => true,
        ],

        // Home page permissions
        'home_page_view' => [
            'name' => 'home_page_view',
            'display_name' => 'View Home Page',
            'description' => 'Can view the home page',
            'is_system' => true,
        ],
        'home_page_create' => [
            'name' => 'home_page_create',
            'display_name' => 'Create Home Page Content',
            'description' => 'Can create new content for the home page',
            'is_system' => true,
        ],
        'home_page_update' => [
            'name' => 'home_page_update',
            'display_name' => 'Update Home Page Content',
            'description' => 'Can update existing content on the home page',
            'is_system' => true,
        ],
        'home_page_delete' => [
            'name' => 'home_page_delete',
            'display_name' => 'Delete Home Page Content',
            'description' => 'Can delete content from the home page',
            'is_system' => true,
        ],
        'home_page_restore' => [
            'name' => 'home_page_restore',
            'display_name' => 'Restore Home Page Content',
            'description' => 'Can restore deleted content on the home page',
            'is_system' => true,
        ],

        // Contact Information permissions
        'contact_view' => [
            'name' => 'contact_view',
            'display_name' => 'View Contact Information',
            'description' => 'Can view contact information',
            'is_system' => true,
        ],
        'contact_update' => [
            'name' => 'contact_update',
            'display_name' => 'Update Contact Information',
            'description' => 'Can update contact information',
            'is_system' => true,
        ],

        // Message permissions
        'message_view' => [
            'name' => 'message_view',
            'display_name' => 'View Messages',
            'description' => 'Can view contact messages',
            'is_system' => true,
        ],
        'message_respond' => [
            'name' => 'message_respond',
            'display_name' => 'Respond to Messages',
            'description' => 'Can respond to contact messages',
            'is_system' => true,
        ],
        'message_update' => [
            'name' => 'message_update',
            'display_name' => 'Update Messages',
            'description' => 'Can update message status',
            'is_system' => true,
        ],
        'message_archive' => [
            'name' => 'message_archive',
            'display_name' => 'Archive Messages',
            'description' => 'Can archive contact messages',
            'is_system' => true,
        ],
        'message_delete' => [
            'name' => 'message_delete',
            'display_name' => 'Delete Messages',
            'description' => 'Can delete contact messages',
            'is_system' => true,
        ],
        'message_restore' => [
            'name' => 'message_restore',
            'display_name' => 'Restore Messages',
            'description' => 'Can restore deleted contact messages',
            'is_system' => true,
        ],

        // Page permissions
        'page_view' => [
            'name' => 'page_view',
            'display_name' => 'View Pages',
            'description' => 'Can view pages',
            'is_system' => true,
        ],
        'page_create' => [
            'name' => 'page_create',
            'display_name' => 'Create Pages',
            'description' => 'Can create new pages',
            'is_system' => true,
        ],
        'page_update' => [
            'name' => 'page_update',
            'display_name' => 'Update Pages',
            'description' => 'Can update existing pages',
            'is_system' => true,
        ],
        'page_delete' => [
            'name' => 'page_delete',
            'display_name' => 'Delete Pages',
            'description' => 'Can delete pages',
            'is_system' => true,
        ],
        'page_restore' => [
            'name' => 'page_restore',
            'display_name' => 'Restore Pages',
            'description' => 'Can restore deleted pages',
            'is_system' => true,
        ],
        'page_publish' => [
            'name' => 'page_publish',
            'display_name' => 'Publish Pages',
            'description' => 'Can publish or unpublish pages',
            'is_system' => true,
        ],

        // Page Image permissions
        'page_image_view' => [
            'name' => 'page_image_view',
            'display_name' => 'View Page Images',
            'description' => 'Can view page images',
            'is_system' => true,
        ],
        'page_image_create' => [
            'name' => 'page_image_create',
            'display_name' => 'Create Page Images',
            'description' => 'Can upload new page images',
            'is_system' => true,
        ],
        'page_image_update' => [
            'name' => 'page_image_update',
            'display_name' => 'Update Page Images',
            'description' => 'Can update existing page images',
            'is_system' => true,
        ],
        'page_image_delete' => [
            'name' => 'page_image_delete',
            'display_name' => 'Delete Page Images',
            'description' => 'Can delete page images',
            'is_system' => true,
        ],
        'page_image_restore' => [
            'name' => 'page_image_restore',
            'display_name' => 'Restore Page Images',
            'description' => 'Can restore deleted page images',
            'is_system' => true,
        ],
    ],
    'reserved_permissions' => [
        'user_view',
        'user_create',
        'user_update',
        'user_delete',
        'user_restore',
        'role_view',
        'role_create',
        'role_update',
        'role_delete',
        'permission_view',
        'permission_create',
        'permission_update',
        'permission_delete',
        'career_view',
        'career_create',
        'career_update',
        'career_delete',
        'career_restore',
        'home_page_view',
        'home_page_create',
        'home_page_update',
        'home_page_delete',
        'home_page_restore',
        'contact_view',
        'contact_update',
        'message_view',
        'message_respond',
        'message_update',
        'message_archive',
        'message_delete',
        'message_restore',
        'page_view',
        'page_create',
        'page_update',
        'page_delete',
        'page_restore',
        'page_publish',
        'page_image_view',
        'page_image_create',
        'page_image_update',
        'page_image_delete',
        'page_image_restore',
    ],
];
