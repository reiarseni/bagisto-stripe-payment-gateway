<?php

return [
    [
        'key'    => 'sales.payment_methods.stripe',
        'info'   => 'Stripe extension.',
        'name'   => 'Stripe',
        'sort'   => 7,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'Stripe',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'Stripe Gateway',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name'          => 'logo_image',
                'title'         => 'Logo',
                'type'          => 'image',
                'channel_based' => false,
                'locale_based'  => false,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
            [
                'name'          => 'stripe_api_key',
                'title'         => 'Stripe Secret Key',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ]
        ]
    ]
];
