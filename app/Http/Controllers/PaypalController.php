<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;

class PaypalController extends Controller
{
    public function payment()
    {
        $items = [
            [
                'name' => 'Product 1',
                'price' => 9,
                'qty' => 1,
            ],
            [
                'name' => 'Product 2',
                'price' => 5,
                'qty' => 2,
            ],
        ];

        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $data = [
            'items' => $items,
            'invoice_id' => 1,
            'invoice_description' => "Order #1 Invoice",
            'return_url' => 'http://127.0.0.1:8000/payment/success',
            'cancel_url' => 'http://127.0.0.1:8000/payment/cancel',
            'total' => $total,
        ];

        $provider = new ExpressCheckout;
        $response = $provider->setExpressCheckout($data);
        //dd($response);

        if ($response['ACK'] === 'Success') {
            return redirect($response['paypal_link']);
        } else {
            dd('Something went wrong: ' . $response['L_LONGMESSAGE0']);
        }
    }

    public function cancel()
    {
        dd('Your payment is canceled. You can create a cancel page here.');
    }

    public function success(Request $request)
    {
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            return response()->json("Paid successfully.");
        } else {
            dd('Something is wrong: ' . $response['L_LONGMESSAGE0']);
        }
    }
}
