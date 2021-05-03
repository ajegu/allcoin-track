<?php


namespace Test\Repository;


use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Database\DynamoDb\ItemManagerInterface;
use AllCoinCore\Helper\SerializerHelper;
use AllCoinCore\Model\Price;
use AllCoinTrack\Repository\PriceRepository;
use DateTime;
use Test\TestCase;

class PriceRepositoryTest extends TestCase
{
    private PriceRepository $priceRepository;

    private ItemManagerInterface $itemManager;
    private SerializerHelper $serializerHelper;

    public function setUp(): void
    {
        $this->itemManager = $this->createMock(ItemManagerInterface::class);
        $this->serializerHelper = $this->createMock(SerializerHelper::class);

        $this->priceRepository = new PriceRepository(
            $this->itemManager,
            $this->serializerHelper
        );
    }

    /**
     * @throws ItemSaveException
     */
    public function testSaveShouldBeOK(): void
    {
        $price = $this->createMock(Price::class);
        $pair = 'foo';
        $price->expects($this->once())->method('getPair')->willReturn($pair);
        $createdAt = new DateTime();
        $price->expects($this->once())->method('getCreatedAt')->willReturn($createdAt);

        $data = [];
        $this->serializerHelper->expects($this->once())
            ->method('normalize')
            ->with($price)
            ->willReturn($data);

        $this->itemManager->expects($this->once())
            ->method('save')
            ->with(
                $data,
                PriceRepository::PARTITION_KEY . "_" . $pair,
                $createdAt->getTimestamp()
            );

        $this->priceRepository->save($price);
    }
}
