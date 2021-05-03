<?php


namespace AllCoinTrack\Repository;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Model\Price;
use AllCoinCore\Repository\Repository;
use DateTime;

class PriceRepository extends Repository implements PriceRepositoryInterface
{
    const PARTITION_KEY = 'price';

    /**
     * @param Price $price
     * @throws ItemSaveException
     */
    public function save(Price $price): void
    {
        $data = $this->serializer->normalize($price);

        $this->itemManager->save(
            $data,
            self::PARTITION_KEY . '_' . $price->getPair(),
            $price->getCreatedAt()->getTimestamp()
        );
    }

    /**
     * @param string $pair
     * @param DateTime $start
     * @param DateTime $end
     * @return Price[]
     * @throws ItemReadException
     */
    public function findAllByDateRange(string $pair, DateTime $start, DateTime $end): array
    {
        $items = $this->itemManager->fetchAllBetween(
            self::PARTITION_KEY . "_" . $pair,
            start: (string)$start->getTimestamp(),
            end: (string)$end->getTimestamp(),
        );

        return array_map(function (array $item) {
            return $this->serializer->deserialize($item, Price::class);
        }, $items);
    }

}
