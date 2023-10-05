<?php

use App\DataInjection\Injections\Injection;
use App\Models\SalesTax;
use App\Models\User;

return new class extends Injection {

    private const EXISTED_TAXES = [
        'AZ' => 8.6,
        'FL' => 7.02,
        'GA' => 7.4,
        'IL' => 8.82,
        'MD' => 6,
        'MA' => 6.25,
        'MI' => 6,
        'MO' => 6.5,
        'NJ' => 6.6,
        'NM' => 7.72,
        'OH' => 7.24,
        'PA' => 6.34,
        'VA' => 6,
        'WA' => 8.86,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /** @var User $cronUser */
        $cronUser = User::query()->where('email', User::EMAIL_FOR_CRON_USER)->first();
        if (!$cronUser) {
            $cronUser = User::query()->find(1);
        }
        foreach (SalesTax::AVAILABLE_STATE_NAMES as $stateCode => $stateName) {
            SalesTax::query()->updateOrCreate(['state_code' => $stateCode], [
                'state_code' => $stateCode,
                'tax' => self::EXISTED_TAXES[$stateCode] ?? 0,
                'created_by' => $cronUser->getKey(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
