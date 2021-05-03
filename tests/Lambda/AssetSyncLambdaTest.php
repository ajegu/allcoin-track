<?php


namespace Test\Lambda;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Helper\DateTimeHelper;
use AllCoinCore\Model\Asset;
use AllCoinTrack\Lambda\AssetSyncLambda;
use AllCoinTrack\Repository\AssetRepositoryInterface;
use DateTime;
use Http\Client\HttpClient;
use Illuminate\Http\Response;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Test\TestCase;

class AssetSyncLambdaTest extends TestCase
{
    private AssetSyncLambda $binanceAssetSyncLambda;

    private HttpClient $client;
    private AssetRepositoryInterface $assetRepository;
    private DateTimeHelper $dateTimeHelper;
    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->client = $this->createMock(HttpClient::class);
        $this->assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $this->dateTimeHelper = $this->createMock(DateTimeHelper::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->binanceAssetSyncLambda = new AssetSyncLambda(
            $this->client,
            $this->assetRepository,
            $this->dateTimeHelper,
            $this->logger,
        );
    }

    /**
     * @throws ItemReadException
     * @throws ItemSaveException
     * @throws ClientExceptionInterface
     */
    public function testInvokeWithBadStatusCodeShouldStop(): void
    {
        $request = new Request(
            method: \Illuminate\Http\Request::METHOD_GET,
            uri: AssetSyncLambda::BINANCE_URI
        );

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_I_AM_A_TEAPOT);

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $this->logger->expects($this->once())->method('warning');

        $this->assetRepository->expects($this->never())->method('exists');
        $this->dateTimeHelper->expects($this->never())->method('now');
        $this->assetRepository->expects($this->never())->method('save');

        $this->binanceAssetSyncLambda->__invoke([]);
    }

    /**
     * @throws ItemReadException
     * @throws ItemSaveException
     * @throws ClientExceptionInterface
     */
    public function testInvokeWithNotNeedAssetPairShouldStop(): void
    {
        $request = new Request(
            method: \Illuminate\Http\Request::METHOD_GET,
            uri: AssetSyncLambda::BINANCE_URI
        );

        $symbols = [
            'data' => [
                [
                    'b' => 'foo',
                    'q' => 'bar'
                ]
            ]
        ];

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())
            ->method('__toString')
            ->willReturn(json_encode($symbols));

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->expects($this->once())->method('getBody')->willReturn($body);

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $this->logger->expects($this->never())->method('warning');
        $this->assetRepository->expects($this->never())->method('exists');
        $this->dateTimeHelper->expects($this->never())->method('now');
        $this->assetRepository->expects($this->never())->method('save');

        $this->binanceAssetSyncLambda->__invoke([]);
    }

    /**
     * @throws ItemReadException
     * @throws ItemSaveException
     * @throws ClientExceptionInterface
     */
    public function testInvokeShouldBeOKWithNonExistingAssetPair(): void
    {
        $request = new Request(
            method: \Illuminate\Http\Request::METHOD_GET,
            uri: AssetSyncLambda::BINANCE_URI
        );

        $base = 'foo';
        $quote = 'USDT';
        $pair = $base . $quote;
        $symbols = [
            'data' => [
                [
                    'b' => $base,
                    'q' => $quote
                ]
            ]
        ];

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())
            ->method('__toString')
            ->willReturn(json_encode($symbols));

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->expects($this->once())->method('getBody')->willReturn($body);

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);


        $this->logger->expects($this->never())->method('warning');

        $this->assetRepository->expects($this->once())
            ->method('exists')
            ->with($pair)
            ->willReturn($this->createMock(Asset::class));

        $this->dateTimeHelper->expects($this->never())->method('now');
        $this->assetRepository->expects($this->never())->method('save');

        $this->binanceAssetSyncLambda->__invoke([]);
    }

    /**
     * @throws ItemReadException
     * @throws ItemSaveException
     * @throws ClientExceptionInterface
     */
    public function testInvokeShouldBeOKWithExistingAssetPair(): void
    {
        $request = new Request(
            method: \Illuminate\Http\Request::METHOD_GET,
            uri: AssetSyncLambda::BINANCE_URI
        );

        $base = 'foo';
        $quote = 'USDT';
        $pair = $base . $quote;
        $symbols = [
            'data' => [
                [
                    'b' => $base,
                    'q' => $quote
                ]
            ]
        ];

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())
            ->method('__toString')
            ->willReturn(json_encode($symbols));

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->expects($this->once())->method('getBody')->willReturn($body);

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $this->logger->expects($this->never())->method('warning');

        $this->assetRepository->expects($this->once())
            ->method('exists')
            ->with($pair)
            ->willReturn(null);

        $now = new DateTime();
        $this->dateTimeHelper->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $asset = new Asset();
        $asset->setPair($pair);
        $asset->setBase($base);
        $asset->setQuote($quote);
        $asset->setCreatedAt($now);

        $this->assetRepository->expects($this->once())
            ->method('save')
            ->with($asset);

        $this->binanceAssetSyncLambda->__invoke([]);
    }
}
