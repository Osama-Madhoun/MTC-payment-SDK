<?php
return [
    #PayPal
    'PAYPAL_CLIENT_ID' => env('PAYPAL_CLIENT_ID'),
    'PAYPAL_SECRET' => env('PAYPAL_SECRET'),
    'PAYPAL_CURRENCY' => env('PAYPAL_CURRENCY', "USD"),
    'PAYPAL_MODE' => env('PAYPAL_MODE',"sandbox"),//sandbox or live

    #Bank of Palestine "BOP"
    'BOP_VERSION' => env('BOP_VERSION',"1.0.0"),//version
    'BOP_MER_ID_USD' => env('BOP_MER_ID_USD'), //Merchant ID USD
    'BOP_MER_ID_ILS' => env('BOP_MER_ID_ILS'), //Merchant ID ILS
    'BOP_MER_ID_JOD' => env('BOP_MER_ID_JOD'), //Merchant ID JOD
    'BOP_CURRENCY_USD' => env('BOP_CURRENCY_USD','840'),
    'BOP_CURRENCY_ILS' => env('BOP_CURRENCY_ILS','376'),
    'BOP_CURRENCY_JOD' => env('BOP_CURRENCY_JOD','400'),
    'BOP_PASSWORD' => env('BOP_PASSWORD'), //password
    'BOP_ACQ_ID' => env('BOP_ACQ_ID'), //Acquirer ID
    'BOP_CAPTURE_FLAG' => env('BOP_CAPTURE_FLAG','A'), //Capture Flag

    #Stripe
    'STRIPE_KEY' => env('STRIPE_KEY'),
    'STRIPE_SECRET' => env('STRIPE_SECRET'),

    #CrossPay
    'CROSSPAY_API_KEY' => env('CROSSPAY_API_KEY'),

    #BILL.PS
    'BILL_API_KEY' => env('BILL_API_KEY'),


    #THAWANI
    'THAWANI_API_KEY' => env('THAWANI_API_KEY', ''),
    'THAWANI_URL' => env('THAWANI_URL', "https://uatcheckout.thawani.om/"),
    'THAWANI_PUBLISHABLE_KEY' => env('THAWANI_PUBLISHABLE_KEY', ''),




    'VERIFY_ROUTE_NAME' => "payment-verify",
    'APP_NAME'=>env('APP_NAME'),


];