<?php

require './../vendor/autoload.php';

$stripe = new \Stripe\StripeClient(getenv("STRIPE_PRIV")); // private api key. need store in envvars.

$checkout_session = $stripe->checkout->sessions->create([
  'line_items' => [[
    'price_data' => [
      'currency' => 'sgd',
      'product_data' => [
        'name' => 'Books',
      ],
      'unit_amount' => 2000, // amount is $20.
    ],
    'quantity' => 3, // unit amnt * quantity. Can augment to 1 quantity, listing total price in unit amount
  ]],
  'mode' => 'payment',
  'success_url' => 'https://thedaniel.life/payment-success.php', // these need to be live on vm. Does not work on local
  'cancel_url' => 'https://thedaniel.life/payment-cancel.php', // these need to be live on vm. Does not work on local
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
?>