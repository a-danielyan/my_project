<?php

namespace Tests\CMS;

use Tests\TestCase;

class SyncDeviceTest extends TestCase
{
    public function test_sync_device(): void
    {
        $response = $this->post(self::CMS_DEVICE_SYNC_ROUTE, $this->getDeviceSyncData());
        $response->assertStatus(200);
    }

    private function getDeviceSyncData(): array
    {
        return [
            'devices' => [
                [
                    'client_id' => 1,
                    'name' => 'new device',
                    'status' => 'Active',

                ],
            ],
        ];
    }
}
