<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Http\Repositories\AccountRepository;
use Illuminate\Support\Facades\Cache;

class DeviceService
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }


    public function syncDevice(array $data): void
    {
        //@todo this is only placeholder for now! real implementation will be done later.
        // probably we need create job and store  request data temporary in database. return response to CMS
        // and then process obtained data
        // probably we need the way to  notify CMS if some devices not synced?
        $deviceData = [];
        foreach ($data['devices'] as $device) {
            /*     $accountId = $this->getAccountIdForRemoteClient($device['client_id']);
                 $deviceData[] = [
                     'name' => $device['name'],
                     'account_id' => $accountId,
                     'status' => $device['status'],
                     'cms_device_id' => $device['status'],
                 ];
                 */
        }
    }

    /**
     * @param int $clientId
     * @return int
     * @throws CustomErrorException
     */
    private function getAccountIdForRemoteClient(int $clientId): int
    {
        $account = Cache::remember('client_account_' . $clientId, 300, function () use ($clientId) {
            return $this->accountRepository->first(where: ['cms_id' => $clientId]);
        });

        if (!$account) {
            throw new CustomErrorException('Account for clientId ' . $clientId . ' missed', 422);
        }

        return 1;
    }
}
