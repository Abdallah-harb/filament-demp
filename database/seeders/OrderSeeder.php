<?php

namespace Database\Seeders;

use App\Enum\PaymentMethodsEnum;
use App\Enum\ShippingStatusEnum;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->error('No products found in the database. Please seed products first.');
            return;
        }

        $paymentMethods = [
            PaymentMethodsEnum::Visa->value,
            PaymentMethodsEnum::Cash->value,
            PaymentMethodsEnum::Wallet->value,
        ];

        $shippingStatuses = [
            ShippingStatusEnum::PENDING->value,
            ShippingStatusEnum::IN_PROGRESS->value,
            ShippingStatusEnum::IN_THE_WAY->value,
            ShippingStatusEnum::DELIVERED->value,
        ];

        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        $addresses = [
            '123 Main St, New York, NY 10001',
            '456 Oak Ave, Los Angeles, CA 90001',
            '789 Pine Rd, Chicago, IL 60601',
            '321 Elm St, Houston, TX 77001',
            '654 Maple Dr, Phoenix, AZ 85001',
            '987 Cedar Ln, Philadelphia, PA 19101',
            '147 Birch Ct, San Antonio, TX 78201',
            '258 Willow Way, San Diego, CA 92101',
            '369 Spruce St, Dallas, TX 75201',
            '741 Ash Blvd, San Jose, CA 95101',
        ];

        $this->command->info('Creating 500 orders with order details...');

        for ($i = 1; $i <= 500; $i++) {
            // Random number of items per order (1-5)
            $itemCount = rand(1, 5);

            // Select random products for this order
            $orderProducts = $products->random(min($itemCount, $products->count()));

            $subTotal = 0;
            $orderDetailsData = [];

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 5);
                $price = $product->price;
                $discount = rand(0, 20) / 100 * $price * $quantity; // 0-20% discount
                $total = ($price * $quantity) - $discount;

                $subTotal += $total;

                $orderDetailsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'discount' => round($discount, 3),
                    'price' => round($price, 3),
                    'total' => round($total, 3),
                ];
            }

            // Calculate order totals
            $discountAmount = rand(0, 15) / 100 * $subTotal; // 0-15% additional discount on order
            $totalAmount = $subTotal - $discountAmount;

            // Generate random date within the last 12 months
            $randomDaysAgo = rand(0, 365);
            $createdAt = now()->subDays($randomDaysAgo);

            // Create the order
            $order = Order::create([
                'user_id' => 1,
                'sub_total_amount' => round($subTotal, 3),
                'discount_amount' => round($discountAmount, 3),
                'total_amount' => round($totalAmount, 3),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'shipping_address' => $addresses[array_rand($addresses)],
                'shipping_status' => $shippingStatuses[array_rand($shippingStatuses)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Create order details
            foreach ($orderDetailsData as $detailData) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $detailData['product_id'],
                    'quantity' => $detailData['quantity'],
                    'discount' => $detailData['discount'],
                    'price' => $detailData['price'],
                    'total' => $detailData['total'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            if ($i % 50 == 0) {
                $this->command->info("Created {$i} orders...");
            }
        }

        $this->command->info('Successfully created 500 orders with order details!');
    }
}
