<?php


namespace AllCoinTrack\Process;



use AllCoinCore\Builder\AssetBuilder;
use AllCoinCore\Builder\AssetPairBuilder;
use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Dto\RequestDtoInterface;
use AllCoinCore\Dto\ResponseDtoInterface;
use AllCoinCore\Process\ProcessInterface;
use AllCoinCore\Repository\AssetPairRepositoryInterface;
use AllCoinCore\Repository\AssetRepositoryInterface;
use Http\Client\HttpClient;
use Illuminate\Http\Response;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;

class BinanceAssetSyncProcess implements ProcessInterface
{
    const BINANCE_URI = 'https://www.binance.com/bapi/asset/v2/public/asset-service/product/get-products?includeEtf=true';

    const NEEDED_ASSET_PAIRS = ['USDT'];

    public function __construct(
        private HttpClient $client,
        private AssetRepositoryInterface $assetRepository,
        private AssetPairRepositoryInterface $assetPairRepository,
        private LoggerInterface $logger,
        private AssetBuilder $assetBuilder,
        private AssetPairBuilder $assetPairBuilder
    )
    {
    }

    /**
     * @param RequestDtoInterface|null $dto
     * @param array $params
     * @return ResponseDtoInterface|null
     * @throws ItemReadException
     * @throws ItemSaveException
     * @throws ClientExceptionInterface
     */
    public function handle(RequestDtoInterface $dto = null, array $params = []): ?ResponseDtoInterface
    {
        $request = new Request(
            method: \Illuminate\Http\Request::METHOD_GET,
            uri: self::BINANCE_URI
        );

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $result = json_decode($response->getBody(), true);

            foreach ($result['data'] as $symbol) {
                $assetName = $symbol['b'];
                $assetPairName = $symbol['q'];

                if (!in_array($assetPairName, self::NEEDED_ASSET_PAIRS)) {
                    continue;
                }

                if (!$asset = $this->assetRepository->existsByName($assetName)) {
                    $asset = $this->assetBuilder->build($assetName);
                    $this->assetRepository->save($asset);
                }

                $assetPairs = $this->assetPairRepository->findAllByAssetId($asset->getId());
                $assetPairExists = null;
                foreach ($assetPairs as $assetPair) {
                    if ($assetPair->getName() === $assetPairName) {
                        $assetPairExists = $assetPair;
                    }
                }
                if (null === $assetPairExists) {
                    $assetPair = $this->assetPairBuilder->build($assetPairName);
                    $this->assetPairRepository->save($assetPair, $asset->getId());
                }
            }
        } else {
            $this->logger->warning('The assets could not be sync for Binance', [
                'response' => (string)$response->getBody()
            ]);
        }

        return null;
    }
}
