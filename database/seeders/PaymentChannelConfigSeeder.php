<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentChannelConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = [
            ['channel_code' => 'bank_transfer', 'channel_name' => 'Bank Transfer (VA)', 'fee_type' => 'flat', 'fee_value' => 4000],
            ['channel_code' => 'qris', 'channel_name' => 'QRIS', 'fee_type' => 'percentage', 'fee_value' => 0.7],
            ['channel_code' => 'cstore', 'channel_name' => 'Alfamart / Indomaret', 'fee_type' => 'flat', 'fee_value' => 5000],
            ['channel_code' => 'credit_card', 'channel_name' => 'Credit Card', 'fee_type' => 'percentage', 'fee_value' => 2.9],
            ['channel_code' => 'gopay', 'channel_name' => 'GoPay', 'fee_type' => 'percentage', 'fee_value' => 2.0],
            ['channel_code' => 'shopeepay', 'channel_name' => 'ShopeePay', 'fee_type' => 'percentage', 'fee_value' => 2.0],
            ['channel_code' => 'other', 'channel_name' => 'Other / E-Wallet', 'fee_type' => 'percentage', 'fee_value' => 1.5],
        ];

        foreach ($channels as $channel) {
            \App\Models\PaymentChannelConfig::updateOrCreate(
                ['channel_code' => $channel['channel_code']],
                $channel
            );
        }
    }
}
