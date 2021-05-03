<?php


namespace AllCoinTrack\Lambda;


use AllCoinCore\Database\DynamoDb\Exception\ItemReadException;
use AllCoinCore\Helper\SerializerHelper;
use AllCoinCore\Lambda\Event\LambdaPriceSearchEvent;
use AllCoinCore\Lambda\LambdaInterface;
use AllCoinCore\Model\Price;
use AllCoinTrack\Repository\PriceRepositoryInterface;

class PriceSearchLambda implements LambdaInterface
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
        private SerializerHelper $serializerHelper
    ) {}

    /**
     * @param array $event
     * @return array|null
     * @throws ItemReadException
     */
    public function __invoke(array $event): array|null
    {
        /** @var LambdaPriceSearchEvent $request */
        $request = $this->serializerHelper->deserialize($event, LambdaPriceSearchEvent::class);

        $prices = $this->priceRepository->findAllByDateRange(
            $request->getPair(),
            $request->getStartAt(),
            $request->getEndAt()
        );

        return array_map(function(Price $price) {
            return $this->serializerHelper->normalize($price);
        }, $prices);

    }

}
