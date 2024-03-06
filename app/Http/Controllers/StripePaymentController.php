<?php

namespace App\Http\Controllers;
use Stripe;

use Illuminate\Http\Request;

class StripePaymentController extends Controller
{
    
    public function stripeHostGetMoney(Request $request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        try {
            
            $express_account = $stripe->accounts->create([
                'country' => 'ES',
                'type' => 'express',
                'capabilities' => [
                  'card_payments' => ['requested' => true],
                  'transfers' => ['requested' => true],
                ],
            ]);
           
            // Crea el enlace de cuenta
            $account_link = $stripe->accountLinks->create([
                'account' => $express_account->id,
                'refresh_url' => 'https://example.com/reauth',
                'return_url' => 'https://example.com/return',
                'type' => 'account_onboarding',
            ]);

            // Devuelve la URL del enlace de cuenta
            return response()->json(['account_link_url' => $account_link->url]);
        } catch (\Exception $e) {
            // Maneja cualquier excepción que pueda ocurrir
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMoney(Request $request, $price){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $product=$stripe->products->create([
          'name' => 'Reserva Lovely Holidays',
          'default_price_data' => [
            'unit_amount' => $price*100,
            'currency' => 'eur',
          ],
          'expand' => ['default_price'],
        ]);

        $priceId = $product['default_price']['id'];

        $session=$stripe->checkout->sessions->create([
            'success_url' => 'http://localhost:4200/bookings',
            'line_items' => [
            [
                'price' => $priceId,
                'quantity' => 1,
            ],
            ],
            'mode' => 'payment',
        ]);
        $url = $session['url'];

        $msg = [
            'msg' => 'Checkout',
            'status' => '200',
            'data' => $url 
        ];
        return response()->json($msg);
        
    
    }
}
