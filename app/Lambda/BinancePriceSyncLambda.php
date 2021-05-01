<?php


namespace AllCoinTrack\Lambda;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Lambda\LambdaInterface;
use AllCoinTrack\Process\BinancePriceSyncProcess;

class BinancePriceSyncLambda implements LambdaInterface
{
    public function __construct(
        private BinancePriceSyncProcess $assetPairPriceBinanceCreateProcess
    )
    {
    }

    /**
     * @param array $event
     * @throws ItemReadException
     * @throws ItemSaveException
     */
    public function __invoke(array $event): void
    {
        $this->assetPairPriceBinanceCreateProcess->handle();
    }
}
