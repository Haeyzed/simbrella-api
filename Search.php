<?php


// Basic search across multiple columns
use App\Models\User;

$users = User::search(['name', 'email'], 'john')
    ->get();

// Search with relationship
$posts = Post::search(['title', 'content', 'user.name'], 'laravel')
    ->get();

// Case-sensitive search
$products = Product::search('name', 'iPhone', ['caseSensitive' => true])
    ->get();

// Search with different match types
$users = User::search('email', 'example.com', ['matchType' => 'endsWith'])
    ->get();

// Search with exact match
$orders = Order::search('order_number', 'ORD-12345', ['matchType' => 'exact'])
    ->get();

// Search with multiple terms (OR condition by default)
$products = Product::search('description', ['phone', 'tablet'])
    ->get();

// Search with multiple terms (AND condition)
$products = Product::search('description', ['premium', 'wireless'], ['boolean' => 'and'])
    ->get();

// Search with custom operator
$products = Product::search('price', '100', ['operator' => '>'])
    ->get();
