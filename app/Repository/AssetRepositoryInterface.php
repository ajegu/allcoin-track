<?php


namespace AllCoinTrack\Repository;


use AllCoinCore\Database\DynamoDb\Exception\ItemDeleteException;
use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Model\Asset;

interface AssetRepositoryInterface
{
    /**
     * @return Asset[]
     * @throws ItemReadException
     */
    public function findAll(): array;

    /**
     * @param Asset $asset
     * @throws ItemSaveException
     */
    public function save(Asset $asset): void;

    /**
     * @param string $pair
     * @throws ItemDeleteException
     */
    public function delete(string $pair);

    /**
     * @param string $pair
     * @return Asset
     * @throws ItemReadException
     */
    public function findOne(string $pair): Asset;

    /**
     * @param string $pair
     * @return Asset|null;
     * @throws ItemReadException
     */
    public function exists(string $pair): Asset|null;
}