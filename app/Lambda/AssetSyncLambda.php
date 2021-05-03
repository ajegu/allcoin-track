<?php


namespace AllCoinTrack\Lambda;



use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Helper\DateTimeHelper;
use AllCoinCore\Lambda\LambdaInterface;
use AllCoinCore\Model\Asset;
use AllCoinTrack\Repository\AssetRepositoryInterface;
use Http\Client\HttpClient;
use Illuminate\Http\Response;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;

class AssetSyncLambda implements LambdaInterface
{
    const BINANCE_URI = 'https://www.binance.com/bapi/asset/v2/public/asset-service/product/get-products?includeEtf=true';

    const NEEDED_ASSET_PAIRS = ['USDT'];

    public function __construct(
        private HttpClient $client,
        private AssetRepositoryInterface $assetRepository,
        private DateTimeHelper $dateTimeHelper,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @param array $event
     * @return array|null
     * @throws ClientExceptionInterface
     * @throws ItemReadException
     * @throws ItemSaveException
     */
    public function __invoke(array $event): array|null
    {
        $request = new Request(
            method: \Illuminate\Http\Request::METHOD_GET,
            uri: self::BINANCE_URI
        );

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $this->logger->warning('The assets could not be sync for Binance', [
                'response' => (string)$response->getBody()
            ]);
            return null;
        }

        $result = json_decode($response->getBody(), true);

        foreach ($result['data'] as $symbol) {
            $base = $symbol['b'];
            $quote = $symbol['q'];

            if (!in_array($quote, self::NEEDED_ASSET_PAIRS)) {
                continue;
            }

            $pair = $base . $quote;

            if (!$this->assetRepository->exists($pair)) {

                $asset = new Asset();
                $asset->setPair($pair);
                $asset->setBase($base);
                $asset->setQuote($quote);
                $asset->setCreatedAt($this->dateTimeHelper->now());

                $this->assetRepository->save($asset);
            }
        }
        return null;
    }
}
