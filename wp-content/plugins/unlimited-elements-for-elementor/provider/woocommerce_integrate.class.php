<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UniteCreatorWooIntegrate{
	
	const POST_TYPE_PRODUCT = "product";
	const PRODUCT_TYPE_VARIABLE = "variable";
		
	private $currency;
	private $currencySymbol;
	private $urlCheckout;
	private $urlCart;
	private $urlSite;
	private $urlCurrentPage;
	
	private $isInited = false;
	
	private static $instance;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->init();
	}

	/**
	 * this action should be run inside the product from the widget editor
	 */
	public static function onInsideEditorWooProduct($productID){
		
		if(self::isWooActive() == false)
			return(false);
			
		if(is_numeric($productID) == false)
			return(false);
		
		if(empty($productID))
			return(false);
		
		//run advanced product labels
		if(class_exists("BeRocket_products_label")){
			do_action('berocket_apl_set_label', true, $productID);
		}
			
		
	}
	
	/**
	 * init actions on start, run on "plugins_loaded" filter
	 */
	public static function initActions(){
		
		add_action("ue_woocommerce_product_integrations", array("UniteCreatorWooIntegrate", "onInsideEditorWooProduct"), 10, 1);
		
	}
	
	
	/**
	 * init if not inited
	 */
	private function init(){
		
		if(self::isWooActive() == false)
			return(false);
		
		if($this->isInited == true)
			return(false);
			
		//init
		$this->currency = get_woocommerce_currency();
    	$this->currencySymbol = get_woocommerce_currency_symbol($this->currency);
    	$this->urlCheckout = wc_get_checkout_url();
    	$this->urlCart = wc_get_cart_url();
    	$this->urlSite = home_url();
		$this->urlCurrentPage = UniteFunctionsWPUC::getUrlCurrentPage();
    	    	
    	$this->isInited = true;
		
    	/*
		global $wp;
		echo home_url($wp->request);
    	*/    	
    	
	}
	
	
	/**
	 * return if acf plugin activated
	 */
	public static function isWooActive(){
		
		if(class_exists('WooCommerce'))
			return(true);
		
		return(false);
	}
	
	/**
	 * check and init instance
	 */
	public static function getInstance(){
		
		if(empty(self::$instance))
			self::$instance = new UniteCreatorWooIntegrate();
		
		
		return(self::$instance);
	}
	
	/**
	 * add add to cart data
	 */
	private function addAddToCartData($arrProduct, $productID, $productSku){
		
		$params = "add-to-cart={$productID}";
		
		$urlAddCart = UniteFunctionsUC::addUrlParams($this->urlCurrentPage, $params);
    	$type = UniteFunctionsUC::getVal($arrProduct, "woo_type");
		
    	$arrProduct["woo_link_addcart_cart"] = UniteFunctionsUC::addUrlParams($this->urlCart, $params);
    	$arrProduct["woo_link_addcart_checkout"] = UniteFunctionsUC::addUrlParams($this->urlCheckout, $params);
    	    	
    	//add html ajax add to cart
    	$addCartAttributes = "href=\"{$urlAddCart}\" data-quantity=\"1\" class=\"uc-button-addcart product_type_simple add_to_cart_button ajax_add_to_cart\" data-product_id=\"{$productID}\" data-product_sku=\"{$productSku}\" rel=\"nofollow\"";
		
    	if($type == self::PRODUCT_TYPE_VARIABLE){
    		
    		$urlProduct = get_permalink($productID);
    		
    		$addCartAttributes = "href=\"{$urlProduct}\" class=\"uc-button-addcart\" ";
    	}
    	
    	$arrProduct["woo_addcart_ajax_attributes"] = $addCartAttributes;
    	
		return($arrProduct);
	}
	
	/**
	 * get child product
	 */
	public function getChildProducts($productID){
		
		$productID = (int)$productID;
		
		if(empty($productID))
			return(array());
		
    	$objInfo = wc_get_product($productID);
    	if(empty($objInfo))
    		return(array());
    	
		$type = $objInfo->get_type();
    	    	
		if($type !== "grouped")
			return(array());
    	
		$arrChildren = $objInfo->get_children();
		
		
		if(empty($arrChildren))
			return(array());
			
		return($arrChildren);
	}
	
	
	/**
	 * add from/to prices to variable product
	 */
	private function addPricesFromTo($arrProduct, $arrPrices){
		
		if(empty($arrPrices))
			return($arrProduct);
		
		foreach($arrPrices as $key=>$arrPriceNumbers){
			
			if(empty($arrPriceNumbers)){
				$arrProduct["woo_".$key."_from"] = 0;
				$arrProduct["woo_".$key."_to"] = 0;				
				continue;
			}
			
			$from = array_shift($arrPriceNumbers);
			if(empty($arrPriceNumbers))
				$to = $from;
			else
				$to = $arrPriceNumbers[count($arrPriceNumbers) - 1];
			
			$from = (float)$from;
			$to = (float)$to;
			
			$arrProduct["woo_".$key."_from"] = $from;
			$arrProduct["woo_".$key."_to"] = $to;
		}
		
		//check and clear sale prices
		
		$regularPriceFrom = UniteFunctionsUC::getVal($arrProduct, "woo_regular_price_from");
		$salePriceFrom = UniteFunctionsUC::getVal($arrProduct, "woo_sale_price_from");
		
		if($regularPriceFrom === $salePriceFrom){
			$arrProduct["woo_sale_price_from"] = null;
			$arrProduct["woo_sale_price_to"] = null;
		}
				
		return($arrProduct);
	}
	
	/**
	 * get array of property names
	 */
	private function getArrPropertyNames($prefix = "", $isAddVariableData = false){
		
    	$arrProperties = array(
    		$prefix."sku",
    		$prefix."price",
    		$prefix."regular_price",
    		$prefix."sale_price",
    		$prefix."stock_quantity",
    		$prefix."stock_status",
    		$prefix."weight",
    		$prefix."length",
    		$prefix."width",
    		$prefix."height",
    		$prefix."average_rating",
    		$prefix."review_count"
    	);
		
    	if($isAddVariableData == false)
    		return($arrProperties);
    	
    	$arrVariable = array(
    		$prefix."regular_price_from",
    		$prefix."regular_price_to",
    		$prefix."sale_price_from",
    		$prefix."sale_price_to",
    		$prefix."price_from",
    		$prefix."price_to"
    	);

    	array_splice($arrProperties, 4, 0, $arrVariable);
    	
    	return($arrProperties);
	}
	
	
	/**
	 * get product data
	 */
	private function getProductData($productID){
		
		if(function_exists("wc_get_product") == false)
			return(null);
		
		//wc_get_ac
    	$objInfo = wc_get_product($productID);
		
    	if(empty($objInfo))
    		return(null);
				
    	$arrData = $objInfo->get_data();
		$type = $objInfo->get_type();
    	
    	$arrProperties = $this->getArrPropertyNames();
    	
    	$productSku = UniteFunctionsUC::getVal($arrData, "sku");
    	$salePrice = UniteFunctionsUC::getVal($arrData, "sale_price");
    	$regularPrice = UniteFunctionsUC::getVal($arrData, "regular_price");
    	$price = UniteFunctionsUC::getVal($arrData, "price");
    	
    	if(empty($regularPrice) && !empty($price))
    		$regularPrice = $price;
    	
    	$arrData["regular_price"] = $regularPrice;
    	    	
    	$arrProduct = array();
    	
    	$arrProduct["woo_type"] = $type;
    	    	
    	foreach($arrProperties as $propertyName){
    		
    		$value = UniteFunctionsUC::getVal($arrData, $propertyName);
    		if(is_array($value) == true)
    			continue;
    		    		
    		$arrProduct["woo_".$propertyName] = $value;    		
    	}
		
    	//make the rating stars array
    	$arrWooStars = array();
    	$rating = UniteFunctionsUC::getVal($arrData, "average_rating");
    	$rating = floatval($rating);
    	
    	$arrWooStars = HelperHtmlUC::getRatingArray($rating);
    	$arrProduct["woo_rating_stars"] = $arrWooStars;
    	
    	//add prices of variations
    	
    	if($type == self::PRODUCT_TYPE_VARIABLE){
    		
    		$arrPrices = $objInfo->get_variation_prices();
    		
    		$arrProduct = $this->addPricesFromTo($arrProduct, $arrPrices);
    		
    		$arrProduct["woo_price"] = $arrProduct["woo_price_from"];
    		$arrProduct["woo_regular_price"] = $arrProduct["woo_regular_price_from"];
    		$arrProduct["woo_sale_price"] = $arrProduct["woo_sale_price_from"];
    	}
    	
    	$regularPrice = UniteFunctionsUC::getVal($arrProduct, "woo_regular_price");
    	$salePrice = UniteFunctionsUC::getVal($arrProduct, "woo_sale_price");
    	
    	//count the discout price
    	
    	$discountPercent = 0;
    	if(!empty($salePrice) && !empty($regularPrice)){
    		
    		$discountPercent = ($regularPrice-$salePrice)/$regularPrice*100;
    		$discountPercent = round($discountPercent);
    	}
    	
    	$arrProduct["woo_discount_percent"] = $discountPercent;
    	
    	//add currency
    	$arrProduct["woo_currency"] = $this->currency;
    	$arrProduct["woo_currency_symbol"] = $this->currencySymbol;
		
    	
    	//put add to cart link
    	$arrProduct = $this->addAddToCartData($arrProduct, $productID, $productSku);
    	
    	return($arrProduct);
	}
	
	
	/**
	 * get woocommerce keys without post
	 */
	private function getWooProductKeysNoPost(){
		
		$arrProperties = $this->getArrPropertyNames("woo_", true);
		
		$arrKeys = array();
		$arrKeys[] = "woo_type";
		
		$arrKeys += $arrProperties;
		
    	$arrKeys[] = "woo_rating_stars";
		$arrKeys[] = "woo_discount_percent";
    	$arrKeys[] = "woo_currency";
    	$arrKeys[] = "woo_currency_symbol";
    	$arrKeys[] = "woo_link_addcart_cart";
    	$arrKeys[] = "woo_link_addcart_checkout";
    	$arrKeys[] = "woo_addcart_ajax_attributes";
    	
    	
		return($arrKeys);
	}
	
	
	/**
	 * get woo data by type
	 */
	private function getWooData($postType, $postID){
		
		if(self::isWooActive() == false)
			return(null);
		
		switch($postType){
			case self::POST_TYPE_PRODUCT:
				$arrData = $this->getProductData($postID);
				
				return($arrData);
			break;
			default:
				return(null);
			break;
		}
		
	}
	
	/**
	 * get woo commerce data by type
	 */
	public static function getWooDataByType($postType, $postID){
		
		$objInstance = self::getInstance();
		
		$response = $objInstance->getWooData($postType, $postID);
		
		return($response);
	}
	
	/**
	 * get keys by post id
	 */
	private function getWooKeys($postID){
		
		if(self::isWooActive() == false)
			return(null);
		
		$post = get_post($postID);
		if(empty($post))
			return(null);
		
		$postType = $post->post_type;
		
		$arrData = self::getWooDataByType($postType, $postID);
		if(empty($arrData))
			return(false);
		
		$arrKeys = array_keys($arrData);
		
		
		return($arrKeys);
		
	}
	
	
	/**
	 * get woo keys by post id
	 */
	public static function getWooKeysByPostID($postID){
		
		$instance = self::getInstance();
		
		$response = $instance->getWooKeys($postID);
		
		return($response);
	}
	
	/**
	 * get woo keys without post id
	 */
	public static function getWooKeysNoPost(){
		
		$instance = self::getInstance();
		
		$response = $instance->getWooProductKeysNoPost();
		
		return($response);
	}
	
	
	/**
	 * put filters js
	 */
	private function putHtmlFiltersJS(){
		
		UniteProviderFunctionsUC::addjQueryInclude();
		
		$urlScriptFile = GlobalsUC::$url_assets_internal."js/uc_woocommerce.js";
		
		HelperUC::addScriptAbsoluteUrl($urlScriptFile, "uc_woo_integrate");
	}
	
	
	/**
	 * put html filter - order
	 */
	private function putHtmlFilter_order($params){
				
		$arrOptions = array();
		$arrOptions["name"] = __("Product Name","unlimited-elements-for-elementor");
		$arrOptions["price"] = __("Price","unlimited-elements-for-elementor");
				
		$name = "uc_order";
		
		$value = UniteFunctionsUC::getPostGetVariable($name, "", UniteFunctionsUC::SANITIZE_KEY);
		
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrOptions, $value, "name='{$name}' class='uc-woo-filter uc-woo-filter-order'", true);
		
		?>
		<form class="uc-woocommerce-ordering" method="get">
			
			<?php echo $htmlSelect?>
			
		</form>
		
		<?php 
		
		$this->putHtmlFiltersJS();
	}
	
	
	/**
	 * put html filter
	 */
	public function putHtmlFilter($filterName, $params = null){
		
		switch($filterName){
			case "order":
				$this->putHtmlFilter_order($params);
			break;
			default:
				UniteFunctionsUC::throwError("putWooFilter error: filter $filterName not exists");
			break;
		}
		
	}

	
}