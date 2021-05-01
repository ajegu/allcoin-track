<?php


namespace AllCoinTrack\Lambda;



use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Lambda\LambdaInterface;
use AllCoinTrack\Process\BinanceAssetSyncProcess;
use Psr\Http\Client\ClientExceptionInterface;

class BinanceAssetSyncLambda implements LambdaInterface
{
    public function __construct(
        private BinanceAssetSyncProcess$binanceSyncAssetProcess
    )
    {
    }

    /**
     * @param array $event
     * @throws ItemReadException
     * @throws ItemSaveException
     * @throws ClientExceptionInterface
     */
    public function __invoke(array $event): void
    {
        $this->binanceSyncAssetProcess->handle();
    }
}
