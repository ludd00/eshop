<?php

namespace App\Model\Facades;

use App\Model\Entities\Product;
use App\Model\Entities\ShopOrder;
use App\Model\Repositories\ProductRepository;
use App\Model\Repositories\ShopOrderRepository;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;

/**
 * Class ProductsFacade
 * @package App\Model\Facades
 */
class ShopOrdersFacade
{
    /** @var ShopOrderRepository $shopOrderRepository */
    private $shopOrderRepository;

    /**
     * Metoda pro vyhledání objednávek
     * @param array|null $params = null
     * @param int $offset = null
     * @param int $limit = null
     * @return Product[]
     */
    public function findShopOrders(array $params=null,int $offset=null,int $limit=null):array {
        return $this->shopOrderRepository->findAllBy($params,$offset,$limit);
    }

    /**
     * Metoda pro získání jedné objednávky pomocí id
     * @param int $id
     * @return ShopOrder
     * @throws \Exception
     */
    public function getShopOrder(int $id):ShopOrder {
        return $this->shopOrderRepository->find($id);
    }

    /**
     * Metoda pro získání objednávek pomocí id uživatele
     * @param int $userId
     * @return ShopOrder
     * @throws \Exception
     */
    public function findShopOrdersByUser(int $userId) {
        return $this->shopOrderRepository->findAllBy(['user_id'=>$userId]);
    }

    public function saveShopOrder(ShopOrder &$shopOrder){
        return (bool)$this->shopOrderRepository->persist($shopOrder);
    }

    public function __construct(ShopOrderRepository $shopOrderRepository)
    {
        $this->shopOrderRepository = $shopOrderRepository;
    }
}