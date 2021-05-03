<?php


namespace AllCoinTrack\Repository;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Model\Price;
use DateTime;

interface PriceRepositoryInterface
{
    /**
     * @param Price $price
     * @throws ItemSaveException
     */
    public function save(Price $price): void;

    /**
     * @param string $pair
     * @param DateTime $start
     * @param DateTime $end
     * @return Price[]
     * @throws ItemReadException
     */
    public function findAllByDateRange(string $pair, DateTime $start, DateTime $end): array;
}
