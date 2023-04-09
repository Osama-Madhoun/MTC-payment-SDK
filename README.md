# MTC Payment Gateways

[![Awesome](https://cdn.rawgit.com/sindresorhus/awesome/d7305f38d29fed78fa85652e3a63e154dd8e8829/media/badge.svg)](https://github.com/sindresorhus/awesome)
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![Made With Love](https://img.shields.io/badge/Made%20With-Love-orange.svg)](https://github.com/chetanraj/awesome-github-badges)

Payment Helper of Payment Gateways ( PayPal - Stripe - Thawani - Bank of Palestine "BOP" - BILL.PS - CrossPay)


## Supported gateways

- [PayPal](https://paypal.com/)
- [Stripe](https://stripe.com/)
- [Thawani](https://thawani.om/)
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

### MTC-payments.php config file 

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

## Put in your local “.env” this variables that included in *MTC-payments.php* config file 
```text
# PayPal
PAYPAL_CLIENT_ID =
PAYPAL_SECRET =
PAYPAL_CURRENCY = USD
PAYPAL_MODE =

# Stripe
STRIPE_KEY = 
STRIPE_SECRET = 

# THAWANI
THAWANI_API_KEY = 
THAWANI_PUBLISHABLE_KEY = 

# Bank of Palestine BOP
BOP_MER_ID_USD = 
BOP_MER_ID_ILS = 
BOP_MER_ID_JOD = 
BOP_PASSWORD = 
BOP_ACQ_ID = 

# BILL.PS
BILL_API_KEY = 
```
## in your Http\Controllers , create new controller file for example with name “MTCPaymentVerificationController.php” which includes function for payment verification , as follows 
#### *MTCPaymentVerificationController.php*
```php
<?php

namespace App\Http\Controllers; // set your namespace

use Illuminate\Http\Request;
use MTC\Payments\Factories\PaymentFactory;

class MTCPaymentVerificationController extends Controller
{
    /**
     * @param $payment -  payment gateway name
     * @param Request $request
     * @return Request
     */
    public function MTC_payment_verify($payment, Request $request)
    {
        $payment_Gateway = new PaymentFactory();
        
        //verify function
        $payment_verify_Response = $payment_Gateway->get($payment)->verify($request);
//        dd($payment_verify_Response);
           
            // outputs - you can use response for store and update data in your project database
            /*[
                'success'=>true,//or false
                'payment_id'=>"69142727KS887432C", // return payment Unique reference id
                'message'=>"Payment process done Successfully",//message for end users (client)
                'process_data'=>""//payment gateway response
            ]*/
            
        return $payment_verify_Response ;
    }
}
```

## "Web.php" <ins>MUST HAVE Route with name</ins> “payment-verify”

```php
Route::any('/mtc-payments/verify/{payment?}',[MTCPaymentVerificationController::class,'MTC_payment_verify'])->name('payment-verify');
```

## How To Use

```jsx
use MTC\Payments\Classes\PaypalPayment;

$payment = new PaypalPayment();

//pay function
 try {
        $payment->pay(
            $amount, 
            $currency = null, 
            $user_id = null, 
            $user_first_name = null, 
            $user_last_name = null, 
            $user_email = null, 
            $user_phone = null, 
            $source = null
        );
        
        // To show and deal with validation messages, required fields, and error messages for payment gateways
     } catch (\Exception $e) {
          dd($e->getMessage());
     }

//OR you can use
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
	'payment_id'=>"", // Unique refrence code that should stored in your orders table
	'redirect_url'=>"", // redirect url available for some payment gateways that require payment via their own third-party page
	'html'=>"" // rendered html available for some payment gateways like : stripe , Bank of palestine-BOP
]
```
### Factory Pattern Use
you can use this way by pass only method name without payment key word like (Paypal,Stripe,Bill ...etc) 
and the factory will return the payment instance for you .
```php
    $payment = new \MTC\Payments\Factories\PaymentFactory();
    $payment=$payment->get(string $paymentName)->pay(
	$amount, 
	$currency = null, 
	$user_id = null, 
	$user_first_name = null, 
	$user_last_name = null, 
	$user_email = null, 
	$user_phone = null, 
	$source = null
);
```
## Available Classes

```php

use MTC\Payments\Classes\PayPalPayment;
use MTC\Payments\Classes\BopPayment;
use MTC\Payments\Classes\StripePayment;
use MTC\Payments\Classes\BillPayment;
use MTC\Payments\Classes\ThawaniPayment;

```
## Payment Gateway Field
#####[ amount,currency,user_id,user_first_name,user_last_name,user_email,user_phone,source]
            
| Payment Gateway | Required Field 
| :--- :       |     :---:     
| **PayPal**   | *amount*     
| **Stripe**     | *amount , currency*   
| **Bank Of Palestine ( BOP )**    | *amount , currency* 
| **Bill.ps**     | *amount , currency , user_first_name , user_last_name , user_email , user_phone*
| **Thawani**     |  *amount  , user_first_name , user_last_name , user_email , user_phone*

## Test Cards

- [Thawani](https://docs.thawani.om/docs/thawani-ecommerce-api/ZG9jOjEyMTU2Mjc3-thawani-test-card)
- [Stripe](https://stripe.com/docs/testing#cards)
