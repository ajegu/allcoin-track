<?php


namespace AllCoinTrack\Repository;


use AllCoinCore\Database\DynamoDb\ItemManagerInterface;
use AllCoinCore\Helper\SerializerHelper;

abstract class AbstractRepository
{
    public function __construct(
        protected ItemManagerInterface $itemManager,
        protected SerializerHelper $serializer,
    )
    {
    }
}