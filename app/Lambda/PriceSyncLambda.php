<?php


namespace AllCoinTrack\Lambda;


use Ajegu\BinanceSdk\Client;
use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Helper\DateTimeHelper;
use AllCoinCore\Lambda\LambdaInterface;
use AllCoinCore\Model\Price;
use AllCoinTrack\Repository\AssetRepositoryInterface;
use AllCoinTrack\Repository\PriceRepositoryInterface;
use Psr\Log\LoggerInterface;

class PriceSyncLambda implements LambdaInterface
{
    public function __construct(
        private Client $client,
        private AssetRepositoryInterface $assetRepository,
        private PriceRepositoryInterface $priceRepository,
        private DateTimeHelper $dateTimeHelper,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @param array $event
     * @return array|null
     * @throws ItemReadException
     * @throws ItemSaveException
     */
    public function __invoke(array $event): array|null
    {
        $assets = $this->assetRepository->findAll();

        foreach ($assets as $asset) {
            $pair = $asset->getPair();

            $bookTicker = $this->client->getBookTicker(['symbol' => $pair]);

            $price = new Price();
            $price->setPair($pair);
            $price->setAskPrice($bookTicker->getAskPrice());
            $price->setBidPrice($bookTicker->getBidPrice());
            $price->setCreatedAt($this->dateTimeHelper->now());

            $this->priceRepository->save($price);

            $this->logger->debug("Save price for $pair");
        }

        return null;
    }
}
