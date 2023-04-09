<?php

namespace MTC\Payments\Classes;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MTC\Payments\Exceptions\MissingPaymentInfoException;
use MTC\Payments\Interfaces\PaymentInterface;
use MTC\Payments\Classes\BaseController;
use Exception;



class BillPayment extends BaseController implements PaymentInterface
{

    private $bill_api_key;
    private $verify_route_name;
    public $app_name;


    public function __construct()
    {
        $this->bill_api_key = config('MTC-payments.BILL_API_KEY');
        $this->verify_route_name = config('MTC-payments.VERIFY_ROUTE_NAME');
        $this->app_name = config('MTC-payments.APP_NAME');

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

    public function pay(
        $amount = null, $currency = null, $user_id = null, $user_first_name = null, $user_last_name = null, $user_email = null, $user_phone = null, $source = null
    )
    {
        $this->setPassedVariablesToGlobal($amount, $currency, $user_id, $user_first_name, $user_last_name, $user_email, $user_phone, $source);
        $required_fields = ['amount', 'currency', 'user_first_name', 'user_last_name', 'user_email', 'user_phone'];
        $this->checkRequiredFields($required_fields, 'Bill');

        $unique_id = uniqid();
        $apiKey = $this->bill_api_key;
        $return_url = route($this->verify_route_name, ['payment' => "bill", 'payment_id' => $unique_id]);
        $email = $this->user_email;     // User Email
        $mobile = $this->user_phone;             // User phone
        $name = $this->user_first_name . ' ' . $this->user_last_name;    //User Name
        $items = array(array(
            "name" => $this->app_name . " Use MTC - Laravel Payment SDK",
            "quntity" => "1.00",
            "unitPrice" => $this->amount,
            "totalPrice" => $this->amount,
            "currency" => $this->currency   //currency value USD || JOD || ILS
        ));
        $inv_details1 = array("inv_items" => $items,
            "inv_info" => array(array("row_title" => 'Tax', 'row_value' => 0)),
            "user" => array("userName" => $name));
        $inv_details = json_encode($inv_details1, JSON_UNESCAPED_UNICODE);
        $query = http_build_query(array(
            'api_data' => '82e4b4fd3a16ad99229af9911ce8e6d2',
            'invoice_id' => $unique_id,
            'apiKey' => $apiKey,
            'total' => $this->amount,
            'currency' => $this->currency,
            'inv_details' => $inv_details,
            'return_url' => $return_url,
            'email' => $email,
            'mobile' => $mobile,
            'name' => $name
        ), null, "&", PHP_QUERY_RFC3986);

        $url1 = "https://bill.ps/api/createInvoiceByAccount?" . $query;

        return [
            'payment_id' => $unique_id,
            'redirect_url' => $url1,
            'html' => "",
        ];

    }

    public function verify(Request $request): array
    {
        $payment_id = $request->payment_id;
        $is_paid = $request['is_paid'];

        $response = $request->all();

        try {
            if ((int)$is_paid == 1) {
                return [
                    'success' => true,
                    'payment_id' => $payment_id,
                    'message' => __('MTC::messages.PAYMENT_DONE'),
                    'process_data' => $response
                ];

            } else {
                return [
                    'success' => false,
                    'payment_id' => $payment_id,
                    'message' => __('MTC::messages.PAYMENT_FAILED'),
                    'process_data' => $response
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'payment_id' => $payment_id,
                'message' => __('MTC::messages.PAYMENT_FAILED'),
                'process_data' => $e
            ];
        }
    }
}
