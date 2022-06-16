# Extend custom attribute install

Add on for Extend module to account for your special product attributes during installation
if you can't install our module because of your product attributes being missing, you can update this AddWarrantyProductPatch to include all your custom required fields

in this add-on, we add a fdx_carton attribute to the default WARRANTY-1 product upon installing the module

## How to use
- you should have already started the extend module install
- create a app/code/Extend folder if you don't have one already
- checkout this repo in app/code/Extend, you should end up with app/code/Extend/CustomAttributeInstall
- if need be, add your extra required fields (in this module we add only fdx_carton, default value = []
- bin/magento setup:upgrade will allow the extend module to install, with your required product attributes now present on the WARRANTY-1 Sku item

## Note:
To apply this module more than once, you have to delete the entry in the Magento Database's table patch_list
where patch_name = 'Extend\CustomAttributeInstall\Setup\Patch\Data\AddWarrantyProductPatch'

(because patches are applied only once)

