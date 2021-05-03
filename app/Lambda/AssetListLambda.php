<?php


namespace AllCoinTrack\Lambda;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Helper\SerializerHelper;
use AllCoinCore\Lambda\LambdaInterface;
use AllCoinCore\Model\Asset;
use AllCoinTrack\Repository\AssetRepositoryInterface;

class AssetListLambda implements LambdaInterface
{
    public function __construct(
        private AssetRepositoryInterface $assetRepository,
        private SerializerHelper $serializerHelper
    ) {}

    /**
     * @param array $event
     * @return array|null
     * @throws ItemReadException
     */
    public function __invoke(array $event): array|null
    {
        $assets = $this->assetRepository->findAll();

        return array_map(function(Asset $asset) {
            return $this->serializerHelper->normalize($asset);
        }, $assets);
    }

}
