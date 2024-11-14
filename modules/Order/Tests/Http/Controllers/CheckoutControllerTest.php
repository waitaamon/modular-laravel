<?php

namespace Modules\Order\Tests\Http\Controllers;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Mail\OrderReceivedMail;
use Modules\Order\Models\Order;
use Modules\Order\Tests\OrderTestCase;
use Modules\Payment\PayBuddySdk;
use Modules\Payment\PaymentProvider;
use Modules\Product\Database\Factories\ProductFactory;

class CheckoutControllerTest extends OrderTestCase
{
    use DatabaseMigrations;
    public function test_it_successfully_creates_an_order(): void
    {
        Mail::fake();

        $user = UserFactory::new()->create();

        $products = ProductFactory::new()->count(2)->create(
            new Sequence(
                ['name' => 'Very expensive air fryer', 'price_in_cents' => 10000, 'stock' => 10],
                ['name' => 'Macbook Pro M3', 'price_in_cents' => 50000, 'stock' => 10]
            )
        );

        $paymentToken = PayBuddySdk::validToken();


        $response = $this->actingAs($user)
            ->post(route('order::checkout', [
                'payment_token' => $paymentToken,
                'products' => [
                    ['id' => $products->first()->id, 'quantity' => 1],
                    ['id' => $products->last()->id, 'quantity' => 1]
                ]
            ]));

        $order = Order::query()->latest('id')->first();

        $response
            ->assertJson(['order_url' => $order->url()])
            ->assertStatus(201);

        Mail::assertSent(OrderReceivedMail::class, function (OrderReceivedMail $mail) use ( $user) {
            return $mail->hasTo($user->email);
        });

        // Order
        $this->assertTrue($order->user->is($user));
        $this->assertEquals(60000, $order->total_in_cents);
        $this->assertEquals('completed', $order->status);

        //payment
        $payment = $order->lastPayment;
        $this->assertEquals('paid', $payment->status);
        $this->assertEquals(PaymentProvider::PayBuddy , $payment->payment_gateway);
        $this->assertEquals(36, strlen($payment->payment_id));
        $this->assertEquals(60000, $payment->total_in_cents);
        $this->assertTrue($payment->user->is($user));

        // Order Lines
        $this->assertCount(2, $order->lines);

        foreach ($products as $product) {
            /** @var \Modules\Order\Models\OrderLine $orderLine */
            $orderLine = $order->lines->where('product_id', $product->id)->first();
            $this->assertEquals($product->price_in_cents, $orderLine->product_price_in_cents);
            $this->assertEquals(1, $orderLine->quantity);
        }

        $products = $products->fresh();
        $this->assertEquals(9, $products->first()->stock);
        $this->assertEquals(9, $products->last()->stock);
    }

    public function test_it_fails_with_an_invalid_token(): void
    {
        $user = UserFactory::new()->create();

        $product = ProductFactory::new()->create();

        $paymentToken = PayBuddySdk::invalidToken();

        $response = $this->actingAs($user)
            ->postJson(route('order::checkout', [
                'payment_token' => $paymentToken,
                'products' => [
                    ['id' => $product->id, 'quantity' => 1]
                ]
            ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_token']);

        $this->assertEquals(0, Order::count());
    }
}
