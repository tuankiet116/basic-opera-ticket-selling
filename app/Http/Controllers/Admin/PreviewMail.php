<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventModel;
use Illuminate\Http\Request;

class PreviewMail extends Controller
{
    public function previewAskingPayment()
    {
        return view("Mail.AskingPayment")->with([
            "event" => EventModel::first(),
            "bookings" => [],
            "seat" => [
                'price' => 0,
                'discount_price' => 0
            ]
        ]);
    }

    public function previewPaymentSuccess()
    {
        return view("Mail.PaymentSuccess")->with([
            "event" => EventModel::first(),
            "bookings" => [],
            "seat" => [
                'price' => 0,
                'discount_price' => 0
            ]
        ]);
    }
}
