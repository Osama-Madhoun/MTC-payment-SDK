<?php

namespace MTC\Payments\Classes;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use MTC\Payments\Interfaces\PaymentInterface;

use MTC\Payments\Classes\BaseController;

class BopPayment extends BaseController implements PaymentInterface
{
    private $BOP_VERSION;
    private $BOP_MER_ID_USD;
    private $BOP_MER_ID_ILS;
    private $BOP_MER_ID_JOD;
    private $BOP_CURRENCY_USD;
    private $BOP_CURRENCY_ILS;
    private $BOP_CURRENCY_JOD;
    private $BOP_PASSWORD;
    private $BOP_ACQ_ID;
    private $BOP_CAPTURE_FLAG;
    public $app_name;
    public $verify_route_name;
    public $payment_id;



    public function __construct()
    {
        $this->BOP_VERSION = config('MTC-payments.BOP_VERSION');
        $this->BOP_MER_ID_USD = config('MTC-payments.BOP_MER_ID_USD');
        $this->BOP_MER_ID_ILS = config('MTC-payments.BOP_MER_ID_ILS');
        $this->BOP_MER_ID_JOD = config('MTC-payments.BOP_MER_ID_JOD');
        $this->BOP_CURRENCY_USD = config('MTC-payments.BOP_CURRENCY_USD');
        $this->BOP_CURRENCY_ILS = config('MTC-payments.BOP_CURRENCY_ILS');
        $this->BOP_CURRENCY_JOD = config('MTC-payments.BOP_CURRENCY_JOD');
        $this->BOP_PASSWORD = config('MTC-payments.BOP_PASSWORD');
        $this->BOP_ACQ_ID = config('MTC-payments.BOP_ACQ_ID');
        $this->BOP_CAPTURE_FLAG = config('MTC-payments.BOP_CAPTURE_FLAG');
        $this->app_name = config('MTC-payments.APP_NAME');
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
        $this->checkRequiredFields($required_fields, 'Bank of Palestine "BOP"');

        $unique_id = uniqid();

        $purchaseAmt= $this->amount;
        $purchaseAmt = $purchaseAmt."";

        if($this->currency == "JOD") {
            $currencyExp=3;
            $BOP_CURRENCY=$this->BOP_CURRENCY_JOD;
            $BOP_MER_ID=$this->BOP_MER_ID_JOD;
            $purchaseAmt = $purchaseAmt."0";
            $formattedPurchaseAmt = substr($purchaseAmt,0,9).substr($purchaseAmt,10);
        }else if($this->currency == "ILS"){
            $currencyExp=2;
            $BOP_CURRENCY=$this->BOP_CURRENCY_ILS;
            $BOP_MER_ID=$this->BOP_MER_ID_ILS;
            $formattedPurchaseAmt = substr($purchaseAmt,0,10).substr($purchaseAmt,11);
        }else{
            $currencyExp=1;
            $BOP_CURRENCY=$this->BOP_CURRENCY_USD;
            $BOP_MER_ID=$this->BOP_MER_ID_USD;
            $formattedPurchaseAmt = substr($purchaseAmt,0,10).substr($purchaseAmt,11);
        }

        $BOP_ACQ_ID=$this->BOP_ACQ_ID;
        $BOP_PASSWORD=$this->BOP_PASSWORD;
        $toEncrypt = $BOP_PASSWORD.$BOP_MER_ID.$BOP_ACQ_ID.$unique_id.$formattedPurchaseAmt.$BOP_CURRENCY;
        $sha1Signature = sha1($toEncrypt);
        $base64Sha1Signature = base64_encode(pack("H*",$sha1Signature));
        $signatureMethod = "SHA1";

        $data = [
            'BOP_VERSION'=> $this->BOP_VERSION,
            'BOP_MER_ID'=>$BOP_MER_ID,
            'BOP_ACQ_ID'=>$BOP_ACQ_ID,
            'BOP_CURRENCY'=>$BOP_CURRENCY,
            'CURRENCY_Exp'=>$currencyExp,
            'formattedPurchaseAmt'=>$formattedPurchaseAmt,
            'BOP_CAPTURE_FLAG'=>$this->BOP_CAPTURE_FLAG,
            'base64Sha1Signature'=>$base64Sha1Signature,
            'signatureMethod'=>$signatureMethod,
            'amount' => $this->amount,
            "currency" => $this->currency,
            'user_id' => $this->user_id,
            'user_first_name' => $this->user_first_name,
            'user_last_name' => $this->user_last_name,
            'user_name' => "{$this->user_first_name} {$this->user_last_name}",
            'user_email' => $this->user_email,
            'user_phone' => $this->user_phone,
            'unique_id' => $unique_id,
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

        $MerID = $request['MerID'];
        $AcqID = $request['AcqID'];
        $ResponseCode = intval($request['ResponseCode']);
        $ReasonCode = intval($request['ReasonCode']);
        $ReasonDescr = $request['ReasonCodeDesc'];
        $Ref = $request['ReferenceNo'];
        $PaddedCardNo = $request['PaddedCardNo'];
        $Signature = $request['Signature'];



        try {
            if ($ResponseCode == 1 && $ReasonCode ==1 ){
                return [
                    'success' => true,
                    'payment_id'=>  $request['OrderID'],
                    'message' => __('MTC::messages.PAYMENT_DONE'),
                    'process_data' => $request->all()
                ];
            }
            else{
                return [
                    'success' => false,
                    'payment_id'=>$request['OrderID'],
                    'message' => __('MTC::messages.PAYMENT_FAILED_WITH_CODE', ['ReasonCodeCODE' => $request['ReasonCode'] ,'ResponseCode' => $request['ResponseCode'],'ReasonCodeDesc' => $request['ReasonCodeDesc'] ]),
                    'process_data' => $request->all()
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'payment_id'=>$request['OrderID'],
                'message' => __('MTC::messages.PAYMENT_FAILED'),
                'process_data' => $e
            ];
        }
    }

    private function generate_html($data): string
    {
        return view('MTC::html.bop', ['model' => $this, 'data' => $data])->render();
    }
}