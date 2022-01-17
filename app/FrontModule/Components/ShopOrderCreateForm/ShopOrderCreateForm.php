<?php

namespace App\FrontModule\Components\ShopOrderCreateForm;

use App\FrontModule\Components\CartControl\CartControl;
use App\FrontModule\Components\CartControl\CartControlFactory;
use App\Model\Entities\Cart;
use App\Model\Entities\Category;
use App\Model\Entities\OrderedProduct;
use App\Model\Entities\ShopOrder;
use App\Model\Entities\User;
use App\Model\Facades\CartFacade;
use App\Model\Facades\OrderedProductsFacade;
use App\Model\Facades\ShopOrdersFacade;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;
use Nette\Utils\Json;

/**
 * Class ShopOrderCreateForm
 * @package App\FrontModule\Components\ShopOrderCreateForm
 *
 * @method onFinished()
 * @method onCancel()
 */
class ShopOrderCreateForm extends Form
{

    use SmartObject;

    /** @var callable[] $onFinished */
    public $onFinished = [];
    /** @var callable[] $onCancel */
    public $onCancel = [];
    /** @var UsersFacade $usersFacade */
    private $usersFacade;
    /** @var ShopOrdersFacade $shopOrdersFacade */
    private $shopOrdersFacade;
    /** @var User $user */
    private $user;
    /** @var CartFacade $cartFacade */
    private $cartFacade;
    /** @var OrderedProductsFacade $orderedProductsFacade */
    private $orderedProductsFacade;
    /** @var CartControl $cartControl */
    private $cartControl;


    /**
     * ShopOrderCreateForm constructor.
     * @param Nette\ComponentModel\IContainer|null $parent
     * @param string|null $name
     * @param UsersFacade $usersFacade
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade, Nette\Security\Passwords $passwords, ShopOrdersFacade $shopOrdersFacade, CartFacade $cartFacade, OrderedProductsFacade $orderedProductsFacade, Nette\Security\User $user)
    {
        parent::__construct($parent, $name);
        $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
        $this->usersFacade = $usersFacade;
        $this->shopOrdersFacade = $shopOrdersFacade;
        $this->cartFacade = $cartFacade;
        $this->orderedProductsFacade = $orderedProductsFacade;

        $this->createSubcomponents();
    }

    private function createSubcomponents()
    {
        $this->addText('name', 'Jméno:')
            ->setRequired('Zadejte jméno');

        $this->addText('street', 'Ulice + č. p.:')
            ->setRequired('Zadejte ulici pro doručení');

        $this->addText('city', 'Město:')
            ->setRequired('Zadejte město pro doručení');

        $this->addText('zipCode', 'PSČ:')
            ->setRequired('Zadejte PSČ');

        $this->addTextArea('customerNote', 'Poznámka:');

        $this->addCheckbox('addBillingAddress', 'Zadat fakturační adresu');

        $this->addText('bFirm', 'Název firmy:')
            ->addConditionOn($this['addBillingAddress'], $this::EQUAL, true)
            ->setRequired('Zadejte název firmy');

        $this->addText('bIco', 'IČO/DIČ:')
            ->addConditionOn($this['addBillingAddress'], $this::EQUAL, true)
            ->setRequired('Zadejte IČO');

        $this->addText('bStreet', 'Ulice + č. p.:')
            ->addConditionOn($this['addBillingAddress'], $this::EQUAL, true)
            ->setRequired('Zadejte ulici v adrese pro fakturaci');

        $this->addText('bCity', 'Město:')
            ->addConditionOn($this['addBillingAddress'], $this::EQUAL, true)
            ->setRequired('Zadejte město v adrese pro fakturaci');

        $this->addText('bZipCode', 'PSČ:')
            ->addConditionOn($this['addBillingAddress'], $this::EQUAL, true)
            ->setRequired('Zadejte psč v adrese pro fakturaci');


        $this->addSubmit('confirm', 'Potvrdit objednávku')
            ->onClick[] = function (SubmitButton $button) {

            $shopOrder = new ShopOrder();
            $shopOrder->user = $this->user;

            $address = [
                'name'=>$this->values['name'],
                'street'=>$this->values['street'],
                'city'=>$this->values['city'],
                'zipCode'=>$this->values['zipCode']
            ];

            try {
                $address = Json::encode($address, Json::PRETTY);
            } catch (Nette\Utils\JsonException $e) {
            }

            if($this->values['addBillingAddress']){
                $billingAddress = [
                    'name'=>$this->values['bFirm'],
                    'name'=>$this->values['bIco'],
                    'street'=>$this->values['bStreet'],
                    'city'=>$this->values['bCity'],
                    'zipCode'=>$this->values['bZipCode']
                ];

                try {
                    $billingAddress = Json::encode($billingAddress, Json::PRETTY);
                } catch (Nette\Utils\JsonException $e) {
                }
            }

            $shopOrder->address = $address;
            $shopOrder->billingAddress = $billingAddress;
            $shopOrder->status = 'confirmed';
            $shopOrder->paid = false;
            $shopOrder->customerNote = $this->values['customerNote'];
            $shopOrder->adminNote = null;

            try {
                $cart = $this->cartFacade->getCartByUser($this->user->userId);
            } catch (\Exception $e) {
                /*košík nebyl pro daného uživatele nalezen*/
                //TODO zrušit formulář
                $this->onCancel('Nebyl nalezen košík.');
            }
            $fullPrice = 0;
            foreach ($cart->items as $cartItem) {
                $fullPrice += $cartItem->count * $cartItem->product->price;
            }
            $shopOrder->price = intval($fullPrice);
            $shopOrderId = $this->shopOrdersFacade->saveShopOrder($shopOrder);

            foreach ($cart->items as $cartItem) {
                $orderedProduct = new OrderedProduct();
                $orderedProduct->shopOrder = $shopOrder;
                $orderedProduct->product = $cartItem->product;
                $orderedProduct->count = $cartItem->count;
                $orderedProduct->unitPrice = intval($cartItem->product->price);

                $this->orderedProductsFacade->saveOrderedProduct($orderedProduct);
            }

            $this->cartFacade->deleteCartByUser($this->user);

            $this->onFinished();
        };
        $this->addSubmit('storno', 'zrušit')
            ->setValidationScope([])
            ->onClick[] = function (SubmitButton $button) {
            $this->onCancel();
        };
    }

    public function setCurrentUser(Nette\Security\User $user): void
    {

        try {
            $user = $this->usersFacade->getUser($user->id);
        } catch (\Exception $e) {
            //TODO vyřešit redirect
        }
        $this->user = $user;
    }

}