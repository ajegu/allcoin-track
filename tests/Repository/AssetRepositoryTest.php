<?php


namespace Test\Repository;


use AllCoinCore\Database\DynamoDb\Exception\ItemDeleteException;
use AllCoinCore\Database\DynamoDb\Exception\ItemNotFoundException;
use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Database\DynamoDb\ItemManagerInterface;
use AllCoinCore\Helper\SerializerHelper;
use AllCoinCore\Model\Asset;
use AllCoinTrack\Repository\AssetRepository;
use Test\TestCase;

class AssetRepositoryTest extends TestCase
{
    private AssetRepository $assetRepository;

    private ItemManagerInterface $itemManager;
    private SerializerHelper $serializerHelper;

    public function setUp(): void
    {
        $this->itemManager = $this->createMock(ItemManagerInterface::class);
        $this->serializerHelper = $this->createMock(SerializerHelper::class);

        $this->assetRepository = new AssetRepository(
            $this->itemManager,
            $this->serializerHelper
        );
    }

    /**
     * @throws ItemSaveException
     */
    public function testSaveShouldBeOK(): void
    {
        $asset = $this->createMock(Asset::class);
        $pair = 'foo';
        $asset->expects($this->once())->method('getPair')->willReturn($pair);

        $item = [];
        $this->serializerHelper->expects($this->once())
            ->method('normalize')
            ->with($asset)
            ->willReturn($item);

        $this->itemManager->expects($this->once())
            ->method('save')
            ->with($item, AssetRepository::PARTITION_KEY, $pair);

        $this->assetRepository->save($asset);
    }

    /**
     * @throws ItemReadException
     */
    public function testFindAllShouldBeOK(): void
    {
        $item = [];
        $items = [
            $item
        ];
        $this->itemManager->expects($this->once())
            ->method('fetchAll')
            ->willReturn($items);

        $this->serializerHelper->expects($this->once())
            ->method('deserialize')
            ->with($item, Asset::class)
            ->willReturn($this->createMock(Asset::class));

        $this->assetRepository->findAll();

    }

    /**
     * @throws ItemReadException
     */
    public function testFindOneShouldBeOK(): void
    {
        $assetName = 'foo';
        $item = [];
        $this->itemManager->expects($this->once())
            ->method('fetchOne')
            ->with(
                AssetRepository::PARTITION_KEY,
                $assetName
            )
            ->willReturn($item);

        $this->serializerHelper->expects($this->once())
            ->method('deserialize')
            ->with($item, Asset::class)
            ->willReturn($this->createMock(Asset::class));

        $this->assetRepository->findOne($assetName);

    }

    /**
     * @throws ItemDeleteException
     */
    public function testDeleteShouldBeOK(): void
    {
        $assetId = 'foo';

        $this->itemManager->expects($this->once())
            ->method('delete')
            ->with(
                AssetRepository::PARTITION_KEY,
                $assetId
            );

        $this->assetRepository->delete($assetId);
    }

    /**
     * @throws ItemReadException
     */
    public function testExistsShouldReturnAsset(): void
    {
        $assetName = 'foo';

        $item = [];
        $this->itemManager->expects($this->once())
            ->method('fetchOne')
            ->with(
                AssetRepository::PARTITION_KEY,
                $assetName
            )
            ->willReturn($item);

        $asset = $this->createMock(Asset::class);
        $this->serializerHelper->expects($this->once())
            ->method('deserialize')
            ->with($item, Asset::class)
            ->willReturn($asset);

        $result = $this->assetRepository->exists($assetName);
        $this->assertEquals($asset, $result);
    }

    /**
     * @throws ItemReadException
     */
    public function testExistsShouldReturnNull(): void
    {
        $assetName = 'foo';

        $this->itemManager->expects($this->once())
            ->method('fetchOne')
            ->with(
                AssetRepository::PARTITION_KEY,
                $assetName
            )
            ->willThrowException($this->createMock(ItemNotFoundException::class));

        $this->serializerHelper->expects($this->never())->method('deserialize');

        $result = $this->assetRepository->exists($assetName);
        $this->assertNull($result);
    }
}