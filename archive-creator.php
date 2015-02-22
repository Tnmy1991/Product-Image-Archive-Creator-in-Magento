<?php 
	/* 
	 * Specially for Sofa, Sofa bed or Bed shopping sites.
	 *
	 * This is php script using for copying images from all configurable products and its 
	 * associated products inside the current Magento store. All the images are copied inside 
	 * Company-ImageArchive folder in root directory under specific category folders.
	 *
	 * Author: Tonmoy Malik, Software Developer Trainee
	 *		   Email: tonmoy.malik@hotmail.com
	 * Run these script using cron!! Instructed by: Author
	 */
?>
<?php
	echo $_SERVER['REMOTE_ADDR'];
	echo '</br>';
	if($_SERVER['REMOTE_ADDR']=="XXX.XXX.XXX.XXX") { 
		require_once 'app/Mage.php'; 
		Mage::app('admin');
		
		$collectionConfigurable = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', array('eq' => 'configurable')); //Fetching all configurable products form your store
		
		$targetpath= Mage::getBaseDir('base'); //Set location for the archive
		mkdir($targetpath . "/Company-ImageArchive" ,0777); //Create parent directory for the archive
		
		$targetpath= Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS; //Set variable for further location
		chmod($targetpath, 0777); //Change folder permission
		
		$allCategoryList = array("Sofa Beds","Sofas","Armchairs","Footstools","Ottomans","Blanket Boxes","Headboards","Storage Beds","Bedsteads","Bed Bases","Mattresses "); //Category list present in your store
		$totalCategory =  count($allCategoryList);
		for ($j=0;$j<$totalCategory;$j++){ //Creating directories inside parent directory for all the present category
			$targetpath= Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS; //Directory location
			mkdir($targetpath . $allCategoryList[$j] ,0777); //Create directory
			$targetpath= Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS.$allCategoryList[$j].DS; //Set category folder location
			chmod($targetpath, 0777); //Change folder permission
		}
		
		foreach ($collectionConfigurable as $_configurableproduct) { 
			$_products = Mage::getModel('catalog/product')->load($_configurableproduct->getId()); //Load configurable product
			$product = $_products->getId(); //Get product id
			if( $product != 10951) { //These check is optional
				$model = Mage::getModel('catalog/product'); //Set the model
				$_product = $model->load($product); //Load the specific product
				$name = $_product->getName(); //Get product name
				$categoryIds = $_product->getCategoryIds(); //Get category
				if(count($categoryIds) ){ 
					$firstCategoryId = $categoryIds[0];
					$_category = Mage::getModel('catalog/category')->load($firstCategoryId);
					
					if($_category->getName() == 'Beds & Mattresses') { //This condition is optional
						$firstCategoryId = $categoryIds[1];
						$_category = Mage::getModel('catalog/category')->load($firstCategoryId);
						$category = $_category->getName(); //Get product category name
					} else {
						$category = $_category->getName(); //Get product category name
					}
				}
				$targetpath= Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS.$category.DS; //Set location according product category
				mkdir($targetpath . $name ,0777); //Creating directory for product
				$newLocation = Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS.$category.DS.$name.DS; //Set location
				chmod($newLocation, 0777); //Change folder permission
				mkdir($newLocation . "LifstyleImages" ,0777); //Optional folder
				$lifestyleImagesLocation = Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS.$category.DS.$name.DS.'LifstyleImages'.DS; //Set location
				chmod($lifestyleImagesLocation, 0777); //Change folder permission
				mkdir($newLocation . "CutoutImages" ,0777); //Optional folder
				$cutoutImagesLocation = Mage::getBaseDir('base').DS.'Company-ImageArchive'.DS.$category.DS.$name.DS.'CutoutImages'.DS; //Set location
				chmod($cutoutImagesLocation, 0777); //Change folder permission
				echo "Product Information: " .$category . " - " . $product . " - " . $name . "</br>"; 
				
				/* Copy Gallery images */
				$paths = Mage::getModel('catalog/product')->load($_products->getId())->getMediaGalleryImages(); //Get gallery images
				$i=1;
				foreach($paths as $_image){ //Copying gallery images inside appropriate folder
					$ImageLink = $_image->getPath(); //Image path
					$fileLocation = $lifestyleImagesLocation . "image" . $i . ".jpg"; //creating new image location 
					fopen($fileLocation, "w"); //Open new location for write
					$i++;
					copy($ImageLink,$fileLocation); //copy image in new location
				}
				
				/* Copy images form associated products - This optional */
				$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($_products->getId()); //Get associated product
				$SimpleProduct=$childIds[0];
				$i=1;
				foreach($SimpleProduct as $value){
					$_images = Mage::getModel('catalog/product')->load($value)->getMediaGalleryImages(); //Get gallery images of associated product
					foreach($_images as $_image){ //Copying gallery images inside appropriate folder
						$current=hash_file("md5",$_image->getPath()); //Filter to stop copying same images - Optional
						if($current==$temp){ //Filter
							break;
						} else {
							$ImageLink = $_image->getPath(); //Image path
							$temp = hash_file("md5",$_image->getPath());
						}
						break;
					}
					$fileLocation = $cutoutImagesLocation . "image" . $i . ".jpg"; //creating new image location 
					fopen($fileLocation, "w"); //Open new location for write
					$i++;
					copy($ImageLink,$fileLocation); //copy image in new location
				}
			}
		}
	}
?>
