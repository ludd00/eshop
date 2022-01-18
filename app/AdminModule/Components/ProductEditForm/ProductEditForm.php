<?php

namespace App\AdminModule\Components\ProductEditForm;

use App\Model\Entities\Product;
use App\Model\Entities\ProductBrand;
use App\Model\Entities\ProductSeries;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use App\Model\Facades\BrandsFacade;
use App\Model\Facades\ProductBrandFacade;
use App\Model\Facades\SeriesFacade;
use App\Model\Facades\ProductSeriesFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class ProductEditForm
 * @package App\AdminModule\Components\ProductEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class ProductEditForm extends Form{

    use SmartObject;

    /** @var callable[] $onFinished */
    public $onFinished = [];
    /** @var callable[] $onFailed */
    public $onFailed = [];
    /** @var callable[] $onCancel */
    public $onCancel = [];
    /** @var CategoriesFacade */
    private $categoriesFacade;
    /** @var ProductsFacade $productsFacade */
    private $productsFacade;
  /** @var BrandsFacade $brandsFacade */
  private $brandsFacade;
  /** @var ProductBrandFacade $productBrandFacade */
  private $productBrandFacade;
  /** @var SeriesFacade $seriesFacade */
  private $seriesFacade;
  /** @var ProductSeriesFacade $productSeriesFacade */
  private $productSeriesFacade;

    /**
     * TagEditForm constructor.
     * @param Nette\ComponentModel\IContainer|null $parent
     * @param string|null $name
     * @param ProductsFacade $productsFacade
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, CategoriesFacade $categoriesFacade, ProductsFacade $productsFacade, BrandsFacade  $brandsFacade,ProductBrandFacade $productBrandFacade,SeriesFacade $seriesFacade,ProductSeriesFacade $productSeriesFacade){
        parent::__construct($parent, $name);
        $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
        $this->categoriesFacade=$categoriesFacade;
        $this->productsFacade=$productsFacade;
        $this->brandsFacade=$brandsFacade;
        $this->productBrandFacade =$productBrandFacade;
        $this->seriesFacade = $seriesFacade;
        $this->productSeriesFacade=$productSeriesFacade;
        $this->createSubcomponents();
    }

    private function createSubcomponents(){
        $productId=$this->addHidden('productId');
        $this->addText('title','Název produktu')
            ->setRequired('Musíte zadat název produktu')
            ->setMaxLength(100);

        $this->addText('url','URL produktu')
            ->setMaxLength(100)
            ->addFilter(function(string $url){
                return Nette\Utils\Strings::webalize($url);
            })
            ->addRule(function(Nette\Forms\Controls\TextInput $input)use($productId){
                try{
                    $existingProduct = $this->productsFacade->getProductByUrl($input->value);
                    return $existingProduct->productId==$productId->value;
                }catch (\Exception $e){
                    return true;
                }
            },'Zvolená URL je již obsazena jiným produktem');

        #region kategorie
        $categories=$this->categoriesFacade->findCategories();
        $categoriesArr=[];
        foreach ($categories as $category){
            $categoriesArr[$category->categoryId]=$category->title;
        }
        $this->addSelect('categoryId','Kategorie',$categoriesArr)
            ->setPrompt('--vyberte kategorii--')
            ->setRequired(false);
        #endregion kategorie

        #region vyrobci
        $brands=$this->brandsFacade->findBrands();
        $brandsArr=[];
        foreach ($brands as $brand){
          $brandsArr[$brand->brandId]=$brand->name;
        }
        $this->addSelect('brandId','Výrobce',$brandsArr)
          ->setPrompt('--vyberte výrobce--')
          ->setRequired(false);
        #endregion vyrobci

        #region serie
        $series=$this->seriesFacade->findSeries();
        $seriesArr=[];
        foreach ($series as $serie){
          $seriesArr[$serie->seriesId]=$serie->name;
        }
        $this->addSelect('seriesId','Série',$seriesArr)
          ->setPrompt('--vyberte sérii--')
          ->setRequired(false);
        #endregion serie

        $this->addTextArea('description', 'Popis produktu')
            ->setRequired('Zadejte popis produktu.');

        $this->addText('price', 'Cena')
            ->setHtmlType('number')
            ->addRule(Form::NUMERIC)
            ->setRequired('Musíte zadat cenu produktu');//tady by mohly být další kontroly pro min, max atp.

        $this->addCheckbox('available', 'Nabízeno ke koupi');

        #region obrázek
        $label='Fotka produktu';
        $photoUpload=$this->addUpload('photo',$label);
        //pokud není zadané ID produktu, je nahrání fotky povinné
        $photoUpload //vyžadování nahrání souboru, pokud není známé productId
        ->addConditionOn($productId, Form::EQUAL, '');

        $photoUpload //limit pro velikost nahrávaného souboru
        ->addRule(Form::MAX_FILE_SIZE, 'Nahraný soubor je příliš velký', 1000000);

        $photoUpload //kontrola typu nahraného souboru, pokud je nahraný
        ->addCondition(Form::FILLED)
            ->addRule(function(Nette\Forms\Controls\UploadControl $photoUpload){
                $uploadedFile = $photoUpload->value;
                if ($uploadedFile instanceof Nette\Http\FileUpload){
                    $extension=strtolower($uploadedFile->getImageFileExtension());
                    return in_array($extension,['jpg','jpeg','png']);
                }
                return false;
            },'Je nutné nahrát obrázek ve formátu JPEG či PNG.');
        #endregion obrázek

        $this->addSubmit('ok','uložit')
            ->onClick[]=function(SubmitButton $button){
            $values=$this->getValues('array');
            if (!empty($values['productId'])){
                try{
                    $product=$this->productsFacade->getProduct($values['productId']);
                }catch (\Exception $e){
                    $this->onFailed('Požadovaný produkt nebyl nalezen.');
                    return;
                }
            }else{
                $product=new Product();
            }

            if (!empty($values['brandId'])&&!empty($values['productId'])){
              try {
                $productBrand=$this->productBrandFacade->getProductBrand($values['productId']);
              }catch (\Exception $e){
                $productBrand = new ProductBrand();
              }
            }elseif (!empty($values['productId'])){
              try {
                $productBrand=$this->productBrandFacade->getProductBrand($values['productId']);
                $this->productBrandFacade->deleteProductBrand($productBrand);
              }catch (\Exception $e){}
            }else{
              $productBrand = new ProductBrand();
            }

            if (!empty($values['seriesId'] &&!empty($values['productId']))){
              try {
                $productSeries=$this->productSeriesFacade->getProductSeries($values['productId']);
              }catch (\Exception $e){
                $productSeries = new ProductSeries();
              }
            }elseif (!empty($values['productId'])){
              try {
                $productSeries=$this->productSeriesFacade->getProductSeries($values['productId']);
                $this->productSeriesFacade->deleteProductSeries($productSeries);
              }catch (\Exception $e){}
            }else{
              $productSeries = new ProductSeries();
            }

            $product->assign($values,['title','url','description','available']);
            $product->price=floatval($values['price']);
            $product->category= $this->categoriesFacade->getCategory($values['categoryId']);
            $productBrand->productId= intval($values['productId']);
            $productBrand->brandId = $this->brandsFacade->getBrand($values['brandId'])->brandId;
            $productSeries->productId=intval($values['productId']);
            $productSeries->seriesId=$this->seriesFacade->getSeries($values['seriesId'])->seriesId;
            $this->productsFacade->saveProduct($product);
            if (empty($values['productId'])&&!empty($values['brandId'])){
              $this->productBrandFacade->saveNewProductBrand($productBrand, $product, $values['brandId']);
            }elseif(!empty($values['brandId'])){
                $this->productBrandFacade->saveProductBrand($productBrand);
            }
            if (empty($values['productId'])&&!empty($values['seriesId'])){
              $this->productSeriesFacade->saveNewProductSeries($productSeries, $product, $values['seriesId']);
            }elseif (!empty($values['seriesId'])){
                $this->productSeriesFacade->saveProductSeries($productSeries);
            }
            $this->setValues(['productId'=>$product->productId]);

            //uložení fotky
            if (($values['photo'] instanceof Nette\Http\FileUpload) && ($values['photo']->isOk())){
                try{
                    $this->productsFacade->saveProductPhoto($values['photo'], $product);
                }catch (\Exception $e){
                    $this->onFailed('Produkt byl uložen, ale nepodařilo se uložit jeho fotku.');
                }
            }

            $this->onFinished('Produkt byl uložen.');
        };
        $this->addSubmit('storno','zrušit')
            ->setValidationScope([$productId])
            ->onClick[]=function(SubmitButton $button){
            $this->onCancel();
        };
    }

    /**
     * Metoda pro nastavení výchozích hodnot formuláře
     * @param Product|array|object $values
     * @param bool $erase = false
     * @return $this
     */
    public function setDefaults($values, bool $erase = false):self {

      try {
        $productBrand = $this->productBrandFacade->getProductBrand($values->productId);
        $brand = $this->brandsFacade->getBrand($productBrand->brandId);
      }catch (\Exception $e) {
      }

      try {
        $productSeries = $this->productSeriesFacade->getProductSeries($values->productId);
        $series = $this->seriesFacade->getSeries($productSeries->seriesId);
      }catch (\Exception $e) {
      }
        if ($values instanceof Product){
            $values = [
                'productId'=>$values->productId,
                'categoryId'=>$values->category?$values->category->categoryId:null,
                'title'=>$values->title,
                'url'=>$values->url,
                'description'=>$values->description,
                'price'=>$values->price,
                'available'=>$values->available,
                'brandId'=>$brand->brandId,
                'seriesId'=>$series->seriesId
            ];
        }
        parent::setDefaults($values, $erase);
        return $this;
    }

}