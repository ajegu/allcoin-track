<?php


namespace Test\Lambda;


use Ajegu\BinanceSdk\Client;
use Ajegu\BinanceSdk\Model\BookTickerResponse;
use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Helper\DateTimeHelper;
use AllCoinCore\Model\Asset;
use AllCoinCore\Model\Price;
use AllCoinTrack\Lambda\PriceSyncLambda;
use AllCoinTrack\Repository\AssetRepositoryInterface;
use AllCoinTrack\Repository\PriceRepositoryInterface;
use DateTime;
use Psr\Log\LoggerInterface;
use Test\TestCase;

class PriceSyncLambdaTest extends TestCase
{
    private PriceSyncLambda $getBinancePriceCommand;

    private Client $client;
    private AssetRepositoryInterface $assetRepository;
    private PriceRepositoryInterface $priceRepository;
    private DateTimeHelper $dateTimeHelper;

    public function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $this->priceRepository = $this->createMock(PriceRepositoryInterface::class);
        $this->dateTimeHelper = $this->createMock(DateTimeHelper::class);
        $logger = $this->createMock(LoggerInterface::class);

        $this->getBinancePriceCommand = new PriceSyncLambda(
            $this->client,
            $this->assetRepository,
            $this->priceRepository,
            $this->dateTimeHelper,
            $logger,
        );
    }

    /**
     * @throws ItemReadException
     * @throws ItemSaveException
     */
    public function testInvokeShouldBeOK(): void
    {
        $asset = $this->createMock(Asset::class);
        $pair = 'foo';
        $asset->expects($this->once())->method('getPair')->willReturn($pair);

        $this->assetRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$asset]);

        $bookTicker = $this->createMock(BookTickerResponse::class);
        $bidPrice = '1.2';
        $bookTicker->expects($this->once())->method('getBidPrice')->willReturn($bidPrice);
        $askPrice = '2.1';
        $bookTicker->expects($this->once())->method('getAskPrice')->willReturn($askPrice);

        $this->client->expects($this->once())
            ->method('getBookTicker')
            ->with(['symbol' => $pair])
            ->willReturn($bookTicker);

        $now = new DateTime();
        $this->dateTimeHelper->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $price = new Price();
        $price->setPair($pair);
        $price->setAskPrice($askPrice);
        $price->setBidPrice($bidPrice);
        $price->setCreatedAt($now);

        $this->priceRepository->expects($this->once())
            ->method('save')
            ->with($price);

        $this->getBinancePriceCommand->__invoke([]);
    }
}
