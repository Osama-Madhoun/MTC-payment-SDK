<?php

namespace MTC\Payments\Classes;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use MTC\Payments\Interfaces\PaymentInterface;
use Session;
use Stripe;
use MTC\Payments\Classes\BaseController;

class StripePayment extends BaseController implements PaymentInterface
{
    private $stripe_key;
    private $stripe_secret;
    public $verify_route_name;


    public function __construct()
    {
        $this->stripe_key = config('MTC-payments.STRIPE_KEY');
        $this->stripe_secret = config('MTC-payments.STRIPE_SECRET');
        $this->verify_route_name = config('MTC-payments.VERIFY_ROUTE_NAME');
    }

    /**
     * @param $amount
     * @param $currency
     * @param null $user_id
     * @param null $user_first_name
     * @param null $user_last_name
     * @param null $user_email
     * @param null $user_phone
     * @param null $source
     * @return array|Application|RedirectResponse|Redirector
     */
    public function pay($amount = null,$currency = null, $user_id = null, $user_first_name = null, $user_last_name = null, $user_email = null, $user_phone = null, $source = null)
    {
        $this->setPassedVariablesToGlobal($amount,$currency,$user_id,$user_first_name,$user_last_name,$user_email,$user_phone,$source);
        $required_fields = ['amount','currency'];
        $this->checkRequiredFields($required_fields, 'Stripe');

        $unique_id = uniqid();

        $data = [
            'stripe_secret' => $this->stripe_secret,
            'stripe_key' => $this->stripe_key,
            'user_id' => $this->user_id,
            'user_name' => "{$this->user_first_name} {$this->user_last_name}",
            'user_email' => $this->user_email,
            'user_phone' => $this->user_phone,
            'unique_id' => $unique_id,
            'item_id' => 1,
            'item_quantity' => 1,
            'amount' => $this->amount  * 100,
            "currency" => $this->currency,
            'payment_id'=>$unique_id
        ];


        return [
            'payment_id' => $unique_id,
            'html' => $this->generate_html($data),
            'redirect_url'=>""
        ];

    }

    /**
     * @param Request $request
     * @return array
     */
    public function verify(Request $request): array
    {

        Stripe\Stripe::setApiKey($this->stripe_secret);

        Stripe\Charge::create ([
            "amount" => $request['amount']  * 100,
            "currency" => $request['currency'],
            "source" =>  $request['stripeToken'],
            "description" => "Stripe payment from MTC Payment SDK"
        ]);

        try {
            return [
                'success' => true,
                'payment_id'=>  $request['payment_id'],
                'message' => __('MTC::messages.PAYMENT_DONE'),
                'process_data' => $request->all()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'payment_id'=>$request['payment_id'],
                'message' => __('MTC::messages.PAYMENT_FAILED'),
                'process_data' => $e
            ];
        }
    }

    private function generate_html($data): string
    {
        return view('MTC::html.stripe', ['model' => $this, 'data' => $data])->render();
    }
}