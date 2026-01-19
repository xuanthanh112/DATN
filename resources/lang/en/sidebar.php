<?php   
return [
    'module' => [
        [
            'title' => 'Dashboard',
            'icon' => 'fa fa-database',
            'name' => ['dashboard'],
            'route' => 'dashboard/index',
            'class' => 'special'
        ],
        [
            'title' => 'Revenue Report',
            'icon' => 'fa fa-money',
            'name' => ['report'],
            'subModule' => [
                [
                    'title' => 'By Time',
                    'route' => 'report/time'
                ],
                [
                    'title' => 'By Product',
                    'route' => 'report/product'
                ],
            ]
        ],
        [
            'title' => 'Product Management',
            'icon' => 'fa fa-cube',
            'name' => ['product','attribute'],
            'subModule' => [
                [
                    'title' => 'Product Group Management',
                    'route' => 'product/catalogue/index'
                ],
                [
                    'title' => 'Product Management',
                    'route' => 'product/index'
                ],

            ]
        ],
        [
            'title' => 'Order Management',
            'icon' => 'fa fa-shopping-bag',
            'name' => ['order'],
            'subModule' => [
                [
                    'title' => 'Order Management',
                    'route' => 'order/index'
                ],
                [
                    'title' => 'Deleted Orders',
                    'route' => 'order/trashed'
                ],
            ]
        ],
        [
            'title' => 'Warranty Management',
            'icon' => 'fa fa-shield',
            'name' => ['warranty'],
            'subModule' => [
                [
                    'title' => 'Warranty List',
                    'route' => 'warranty/index'
                ],
                [
                    'title' => 'Statistics',
                    'route' => 'warranty/statistics'
                ],
            ]
        ],
        [
            'title' => 'Customer Group Management',
            'icon' => 'fa fa-user',
            'name' => ['customer'],
            'subModule' => [
                [
                    'title' => 'Customer Group Management',
                    'route' => asset('customer/catalogue/index')
                ],
                [
                    'title' => 'Customer Management',
                    'route' => 'customer/index'
                ],
            ]
        ],
        [
            'title' => 'Marketing Management',
            'icon' => 'fa fa-money',
            'name' => ['promotion'],
            'subModule' => [
                [
                    'title' => 'Promotion Management',
                    'route' => 'promotion/index'
                ],
            ]
        ],
        [
            'title' => 'Article Management',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'Article Group Management',
                    'route' => 'post/catalogue/index'
                ],
                [
                    'title' => 'Article Management',
                    'route' => 'post/index'
                ]
            ]
        ],
        [
            'title' => 'Comment Management',
            'icon' => 'fa fa-comment',
            'name' => ['reviews'],
            'subModule' => [
                [
                    'title' => 'Comment Management',
                    'route' => 'review/index'
                ]
            ]
        ],
        [
            'title' => 'Member Group Management',
            'icon' => 'fa fa-user',
            'name' => ['user','permission'],
            'subModule' => [
                [
                    'title' => 'Member Group Management',
                    'route' => 'user/catalogue/index'
                ],
                [
                    'title' => 'Member Management',
                    'route' => 'user/index'
                ],
                [
                    'title' => 'Permission Management',
                    'route' => 'permission/index'
                ]
            ]
        ],
        [
            'title' => 'Banner & Slide Management',
            'icon' => 'fa fa-picture-o',
            'name' => ['slide'],
            'subModule' => [
                [
                    'title' => 'Slide Settings',
                    'route' => 'slide/index'
                ],
            ]
        ],
        [
            'title' => 'Menu Management',
            'icon' => 'fa fa-bars',
            'name' => ['menu'],
            'subModule' => [
                [
                    'title' => 'Menu Settings',
                    'route' => 'menu/index'
                ],
            ]
        ],
        [
            'title' => 'General Configuration',
            'icon' => 'fa fa-file',
            'name' => ['language', 'generate', 'system', 'widget'],
            'subModule' => [
                [
                    'title' => 'Language Management',
                    'route' => 'language/index'
                ],
                [
                    'title' => 'System Configuration',
                    'route' => 'system/index'
                ],
                [
                    'title' => 'Widget Management',
                    'route' => 'widget/index'
                ],
                
            ]
        ]
    ],
];

