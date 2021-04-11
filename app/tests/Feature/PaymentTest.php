<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations as TestingDatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_shopkeeper_payment()
    {
        $this->createUsers();
        $data = [
            "value" => "10",
            "payer" => "1",
            "payee" => "2"
        ];

        $response = $this->post('/api/payment', $data);

        $response->assertStatus(400);
    }

    public function test_insufficient_balance()
    {
        $this->createUsers();
        $data = [
            "value" => "100000",
            "payer" => "3",
            "payee" => "2"
        ];

        $response = $this->post('/api/payment', $data);

        $response->assertStatus(400);
    }

    public function test_payment_done()
    {
        $this->createUsers();

        $data = [
            "value" => "100",
            "payer" => "2",
            "payee" => "1"
        ];

        $response = $this->post('/api/payment', $data);

        $response->assertStatus(200);
    }

    private function createUsers()
    {
        User::create([
            'id' => 1,
            'name' => "John Doe",
            'email' => "joedohn@gmail.com",
            'tax_identification' => rand(10000000000, 1000000000),
            'wallet' => 1000,
            'isShopkeeper' => 1,
        ]);
        
        User::create([
            'id' => 2,
            'name' => "Maria Silva",
            'email' => "m.silva@gmail.com",
            'tax_identification' => rand(10000000000, 1000000000),
            'wallet' => 1000,
            'isShopkeeper' => 0,
        ]);
        
        User::create([
            'id' => 3,
            'name' => "Sr. Rand",
            'email' => "rand@uol.com",
            'tax_identification' => rand(10000000000, 1000000000),
            'wallet' => 1000,
            'isShopkeeper' => 0,
        ]);
    }
}
