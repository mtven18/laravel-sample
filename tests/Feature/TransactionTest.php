<?php

namespace Tests\Feature;

use App\Enum\Currency;
use App\Enum\TransactionStatus;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_user_transactions()
    {
        $startBalance = 100;
        $amount = 50.05;
        $currency = Currency::USD;

        /*** @var User $user */
        $user = User::factory()->create();

        $this->addBalance($user, $startBalance, $currency);

        $this->actingAs($user);

        /*** @var User $recipient */
        $recipient = User::factory()->create();

        $this->postJson('/api/transactions', [
            'username' => $recipient->username,
            'amount' => $amount,
            'currency' => $currency->value,
        ])->assertCreated();

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'currency' => $currency->value,
            'amount' => $startBalance - $amount,
        ]);
        $this->assertDatabaseHas('balances', [
            'user_id' => $recipient->id,
            'currency' => $currency->value,
            'amount' => $amount,
        ]);

        // Check transactions
        $this->getJson('/api/transactions')
            ->assertJson(function (AssertableJson $json) use ($amount, $recipient, $user) {
                $json->has('data.0', function (AssertableJson $json) use ($amount, $recipient, $user) {
                    $json->where('amount', $amount)
                        ->where('status', TransactionStatus::SUCCESS->value)
                        ->where('from.username', $user->username)
                        ->where('to.username', $recipient->username)
                        ->etc();
                })->etc();
            });
    }

    public function test_transaction_invalid_data()
    {
        /*** @var User $user */
        $user = User::factory()->create();

        $this->addBalance($user, 100, Currency::USD);

        $this->actingAs($user);

        $this->postJson('/api/transactions', [
            'username' => $this->faker->userName,
            'amount' => 0,
            'currency' => Currency::USD->value,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'sum'
            ]);
    }

    public function test_transaction_insufficient_amount()
    {
        /*** @var User $user */
        $user = User::factory()->create();
        $recipient = User::factory()->create();

        $this->addBalance($user, 100, Currency::USD);

        $this->actingAs($user);

        $this->postJson('/api/transactions', [
            'username' => $recipient->username,
            'amount' => 101,
            'currency' => Currency::USD->value,
        ])->assertJsonPath('data.status', TransactionStatus::FAILED->value);
    }

    public function test_transaction_not_found_recipient()
    {
        /*** @var User $user */
        $user = User::factory()->create();

        $this->addBalance($user, 100, Currency::USD);

        $this->actingAs($user);

        $this->postJson('/api/transactions', [
            'username' => $this->faker->userName,
            'amount' => 101,
            'currency' => Currency::USD->value,
        ])->assertForbidden();
    }

    private function getBalanceService(): BalanceService
    {
        /** @var BalanceService $service */
        $service = $this->app->make(BalanceService::class);

        return $service;
    }

    private function addBalance(User $user, float $amount, Currency $currency): void
    {
        $this->getBalanceService()->increaseUserBalance($user, $currency, $amount);
    }
}
