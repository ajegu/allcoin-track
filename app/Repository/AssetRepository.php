<?php


namespace AllCoinTrack\Repository;


use AllCoinCore\Database\DynamoDb\Exception\ItemDeleteException;
use AllCoinCore\Database\DynamoDb\Exception\ItemNotFoundException;
use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Database\DynamoDb\Exception\ItemSaveException;
use AllCoinCore\Model\Asset;
use AllCoinCore\Repository\Repository;

class AssetRepository extends Repository implements AssetRepositoryInterface
{
    const PARTITION_KEY = 'asset';

    /**
     * @return Asset[]
     * @throws ItemReadException
     */
    public function findAll(): array
    {
        $items = $this->itemManager->fetchAll(
            self::PARTITION_KEY
        );

        return array_map(function (array $item) {
            return $this->serializer->deserialize($item, Asset::class);
        }, $items);
    }

    /**
     * @param Asset $asset
     * @throws ItemSaveException
     */
    public function save(Asset $asset): void
    {
        $item = $this->serializer->normalize($asset);

        $this->itemManager->save(
            data: $item,
            partitionKey: self::PARTITION_KEY,
            sortKey: $asset->getPair()
        );
    }

    /**
     * @param string $pair
     * @throws ItemDeleteException
     */
    public function delete(string $pair)
    {
        $this->itemManager->delete(
            self::PARTITION_KEY,
            $pair
        );
    }

    /**
     * @param string $pair
     * @return Asset
     * @throws ItemReadException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function findOne(string $pair): Asset
    {
        $item = $this->itemManager->fetchOne(
            self::PARTITION_KEY,
            $pair
        );

        return $this->serializer->deserialize($item, Asset::class);
    }

    /**
     * @param string $pair
     * @return Asset|null;
     * @throws ItemReadException
     */
    public function exists(string $pair): Asset|null
    {
        try {
            return $this->findOne($pair);
        } catch (ItemNotFoundException) {
            return null;
        }
    }
}
