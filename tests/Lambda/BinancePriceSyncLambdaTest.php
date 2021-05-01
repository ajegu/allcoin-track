<?php


namespace Test\Lambda;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinTrack\Lambda\BinancePriceSyncLambda;
use AllCoinTrack\Process\BinancePriceSyncProcess;
use Test\TestCase;

class BinancePriceSyncLambdaTest extends TestCase
{
    private BinancePriceSyncLambda $getBinancePriceCommand;

    private BinancePriceSyncProcess $assetPairPriceBinanceCreateProcess;

    public function setUp(): void
    {
        $this->assetPairPriceBinanceCreateProcess = $this->createMock(BinancePriceSyncProcess::class);

        $this->getBinancePriceCommand = new BinancePriceSyncLambda(
            $this->assetPairPriceBinanceCreateProcess
        );
    }

    /**
     * @throws ItemReadException
     * @throws ItemSaveException
     */
    public function testInvokeShouldBeOK(): void
    {
        $this->assetPairPriceBinanceCreateProcess->expects($this->once())
            ->method('handle');

        $this->getBinancePriceCommand->__invoke([]);
    }
}