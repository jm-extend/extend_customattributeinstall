<?php
/*
 *  custom add on for Extend module to account for your special product attributes
 *  in this add-on, we add a fdx_carton attribute to the default WARRANTY-1 product upon installing the module
 *  to apply this module more than once, you have to delete the entry in the table patch_list
 *  where patch_name = 'Extend\CustomAttributeInstall\Setup\Patch\Data\AddWarrantyProductPatch'
 *  (because patches are applied only once)
 */

namespace Extend\CustomAttributeInstall\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\ProductFactory;
use Extend\Warranty\Model\Product\Type;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductRepository;


class AddWarrantyProductPatch implements DataPatchInterface
{
    protected ProductFactory $productFactory;
    protected ProductRepository $productRepository;
    protected StoreManagerInterface $storeManager;
    protected State $state;

    public function __construct
    (
        //Product $product,
        ProductRepository $productRepository,
        ProductFactory $productFactory,
        State $state,
        StoreManagerInterface $storeManager
    )
    {
       // $this->product = $product;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->state = $state;
        $this->storeManager = $storeManager;
    }

    public static function getDependencies()
    {
            return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $websites = $this->storeManager->getWebsites();

        /* extend warranty : DO NOT CHANGE ! */
        $sku = 'WARRANTY-1';

        /* define your custom attributes variables here */
        $fdxCartonValue = [];

        try {
            //product exists : update
            $this->storeManager->setCurrentStore(0);
            $product = $this->productRepository->get($sku);
            $product->setWebsiteIds(array_keys($websites));

            /* set your custom attributes here */
            $product->setData('fdx_carton', $fdxCartonValue);

            $product->save();

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            //product doesn't exist : create
            /* update your custom attribute in here too */

            $warranty = $this->productFactory->create();
            $warranty->setSku($sku)
                ->setName('Extend Protection Plan')
                ->setAttributeSetId(4) //Default
                ->setStatus(1) //Enable
                ->setVisibility(1) //Not visible individually
                ->setTaxClassId(0) //None
                ->setTypeId(Type::TYPE_CODE)
                ->setPrice(0.0)
                ->setWebsiteIds(array_keys($websites))
                ->setCreatedAt(strtotime('now'))

                ->setStockData([
                    'use_config_manage_stock' => 0,
                    'is_in_stock' => 1,
                    'qty' => 1,
                    'manage_stock' => 0,
                    'use_config_notify_stock_qty' => 0
                ])
                ->setData('fdx_carton', $fdxCartonValue);

            $imagePath = 'Extend_icon.png';
            $warranty->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);

            $warranty->save();
        }
        return $this;
    }
}
