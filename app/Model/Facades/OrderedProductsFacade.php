<?php

namespace App\Model\Facades;

use App\Model\Entities\OrderedProduct;
use App\Model\Entities\Product;
use App\Model\Entities\ShopOrder;
use App\Model\Repositories\OrderedProductRepository;
use App\Model\Repositories\ProductRepository;
use App\Model\Repositories\ShopOrderRepository;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;

/**
 * Class OrderedProductsFacade
 * @package App\Model\Facades
 */
class OrderedProductsFacade
{
    /** @var OrderedProductsRepository $orderedProductsRepository */
    private $orderedProductsRepository;
    /**
     * @var ShopOrderRepository
     */
    private $shopOrderRepository;

    /**
     * Metoda pro získání objednané položk\
     * @param int $id
     * @return ShopOrder
     * @throws \Exception
     */
    public function getOrderedProduct(int $id):OrderedProduct {
        return $this->orderedProductsRepository->find($id);
    }

    /**
     * Metoda pro získání objednávek pomocí id objednávky
     * @param int $userId
     * @return ShopOrder
     * @throws \Exception
     */
    public function findOrderedProductsByShopOrder(int $shopOrderId): ShopOrder
    {
        return $this->orderedProductsRepository->findAllBy(['shop_order_id'=>$shopOrderId]);
    }

    public function saveOrderedProduct(OrderedProduct &$orderedProduct)
    {
        return $this->orderedProductsRepository->persist($orderedProduct);
    }

    public function __construct(ShopOrderRepository $shopOrderRepository, OrderedProductRepository $orderedProductRepository)
    {
        $this->shopOrderRepository = $shopOrderRepository;
        $this->orderedProductsRepository = $orderedProductRepository;
    }
}