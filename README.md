# MTC Payment Gateways

[![Awesome](https://cdn.rawgit.com/sindresorhus/awesome/d7305f38d29fed78fa85652e3a63e154dd8e8829/media/badge.svg)](https://github.com/sindresorhus/awesome)
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![Made With Love](https://img.shields.io/badge/Made%20With-Love-orange.svg)](https://github.com/chetanraj/awesome-github-badges)

Payment Helper of Payment Gateways ( PayPal - Stripe - Bank of Palestine "BOP" - BILL.PS - CrossPay)


## Supported gateways

- [PayPal](https://paypal.com/)
- [Stripe](https://stripe.com/)
- [Bank of Palestine "BOP"](https://bop.ps/)
- [BILL.PS](https://bill.ps/)
- [CrossPay](https://crosspayonline.com/)

## Installation

```jsx
composer require mtc/payments
```

## Publish Vendor Files

```jsx
php artisan vendor:publish --tag="MTC-payments-config"
php artisan vendor:publish --tag="MTC-payments-lang"
```

### MTC-payments.php file

```php
<?php
return [

    #PayPal
       'PAYPAL_CLIENT_ID' => env('PAYPAL_CLIENT_ID'),
       'PAYPAL_SECRET' => env('PAYPAL_SECRET'),
       'PAYPAL_CURRENCY' => env('PAYPAL_CURRENCY', "USD"),
       'PAYPAL_MODE' => env('PAYPAL_MODE',"sandbox"),//sandbox or live


    #Bank of Palestine "BOP"
       'BOP_VERSION' => env('BOP_VERSION',"1.0.0"),//version, Default is : 1.0.0
       'BOP_MER_ID_USD' => env('BOP_MER_ID_USD'), //Merchant ID USD
       'BOP_MER_ID_ILS' => env('BOP_MER_ID_ILS'), //Merchant ID ILS
       'BOP_MER_ID_JOD' => env('BOP_MER_ID_JOD'), //Merchant ID JOD
       'BOP_CURRENCY_USD' => env('BOP_CURRENCY_USD','840'), // Default is : 840
       'BOP_CURRENCY_ILS' => env('BOP_CURRENCY_ILS','376'), // Default is : 376
       'BOP_CURRENCY_JOD' => env('BOP_CURRENCY_JOD','400'), // Default is : 400
       'BOP_PASSWORD' => env('BOP_PASSWORD'), //password
       'BOP_ACQ_ID' => env('BOP_ACQ_ID'), //Acquirer ID
       'BOP_CAPTURE_FLAG' => env('BOP_CAPTURE_FLAG','A'), //Capture Flag , Default is : A


    #Stripe
       'STRIPE_KEY' => env('STRIPE_KEY'),
       'STRIPE_SECRET' => env('STRIPE_SECRET'),


    #BILL.PS
       'BILL_API_KEY' => env('BILL_API_KEY'),


    #THAWANI
       'THAWANI_API_KEY' => env('THAWANI_API_KEY', ''),
       'THAWANI_URL' => env('THAWANI_URL', "https://uatcheckout.thawani.om/"),
       'THAWANI_PUBLISHABLE_KEY' => env('THAWANI_PUBLISHABLE_KEY', ''),


    #General Setting
       'VERIFY_ROUTE_NAME' => "payment-verify",
       'APP_NAME'=>env('APP_NAME'),
];
```

## " Web.php" MUST HAVE Route with name “payment-verify”

```php
Route::get('/payments/verify/{payment?}',[FrontController::class,'payment_verify'])->name('payment-verify');
```

## How To Use

```jsx
use MTC\Payments\PaypalPayment;

$payment = new PaypalPayment();

//pay function
$payment->pay(
	$amount, 
	$user_id = null, 
	$user_first_name = null, 
	$user_last_name = null, 
	$user_email = null, 
	$user_phone = null, 
	$source = null
);

//or use
$payment->setUserId($id)
        ->setUserFirstName($first_name)
        ->setUserLastName($last_name)
        ->setUserEmail($email)
        ->setUserPhone($phone)
        ->setCurrency($currency)
        ->setAmount($amount)
        ->pay();

//pay function response 
[
	'payment_id'=>"", // refrence code that should stored in your orders table
	'redirect_url'=>"", // redirect url available for some payment gateways
	'html'=>"" // rendered html available for some payment gateways
]

//verify function
$payment->verify($request);

//outputs
[
	'success'=>true,//or false
    'payment_id'=>"PID",
	'message'=>"Done Successfully",//message for client
	'process_data'=>""//payment response
]

```
### Factory Pattern Use
you can pass only method name without payment key word like (Paypal,Stripe,Bill ...etc) 
and the factory will return the payment instance for you , use it as you want ;)
```php
    $payment = new \MTC\Payments\Factories\PaymentFactory();
    $payment=$payment->get(string $paymentName)->pay(
	$amount, 
	$user_id = null, 
	$user_first_name = null, 
	$user_last_name = null, 
	$user_email = null, 
	$user_phone = null, 
	$source = null
);;
```
## Available Classes

```php

use MTC\Payments\Classes\PayPalPayment;
use MTC\Payments\Classes\BopPayment;
use MTC\Payments\Classes\StripePayment;
use MTC\Payments\Classes\BillPayment;
use MTC\Payments\Classes\ThawaniPayment;

```

## Test Cards

- [Thawani](https://docs.thawani.om/docs/thawani-ecommerce-api/ZG9jOjEyMTU2Mjc3-thawani-test-card)
- [Stripe](https://stripe.com/docs/testing#cards)
