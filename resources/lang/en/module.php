<?php   

    return [
        'model' => [
            'PostCatalogue' => 'Article Group',
            'Post' => 'Article',
            'ProductCatalogue' => 'Product Group',
            'Product' => 'Product',
        ],
        'type' => [
            'dropdown-menu' => 'Dropdown Menu',
            'mega-menu' => 'Mega Menu'
        ],
        'effect' => [
            'fade' => 'Fade',
            'cube' => 'Cube',
            'coverflow' => 'Coverflow',
            'flip' => 'Flip',
            'cards' => 'Cards',
            'creative' => 'Creative',
        ],
        'navigate' => [
            'hide' => 'Hide',
            'dots' => 'Dots',
            'thumbnails' => 'Thumbnails'
        ],
        'promotion' => [
            'order_amount_range' => 'Discount by total order value',
            'product_and_quantity' => 'Discount by each product',
        ],
        'item' => [
            'Product' => 'Product variant',
            'ProductCatalogue' => 'Product type',
        ],
        'gender' => [
            [
                'id' => 1,
                'name' => 'Male'
            ],
            [
                'id' => 2,
                'name' => 'Female'
            ],
        ],
        'day' => array_map(function($value){
            return ['id' => $value-1, 'name' => $value];
        }, range(1, 31)),
        'applyStatus' => [
            // Applied object has been removed
        ],
    ];


