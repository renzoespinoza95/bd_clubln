<?php
declare(strict_types=1);

require_once "services/tools/rest.php";
require_once "services/tools/db.php";

require_once "services/table/Product.php";
require_once "services/table/ProductCategory.php";
require_once "services/table/ProductOrder.php";
require_once "services/table/ProductOrderDetail.php";
require_once "services/table/ProductImage.php";
require_once "services/table/Category.php";
require_once "services/table/User.php";
require_once "services/table/NewsInfo.php";
require_once "services/table/AppVersion.php";
require_once "services/table/Currency.php";
require_once "services/table/Config.php";

require_once "client.php";
require_once "dashboard.php";

class API {

    private ?DB $db = null;

    private ?Product $product = null;
    private ?ProductCategory $product_category = null;
    private ?ProductOrder $product_order = null;
    private ?ProductOrderDetail $product_order_detail = null;
    private ?ProductImage $product_image = null;
    private ?Category $category = null;
    private ?User $user = null;
    private ?Fcm $fcm = null;
    private ?NewsInfo $news_info = null;
    private ?Currency $currency = null;
    private ?Config $config = null;
    private ?AppVersion $app_version = null;
    private ?CLIENT $client = null;
    private ?DASHBOARD $dashboard = null;
    private ?Mail $mail = null;

    public function __construct() {

        $this->db = new DB();

        $this->user                  = new User($this->db);
        $this->product               = new Product($this->db);
        $this->product_category      = new ProductCategory($this->db);
        $this->product_order         = new ProductOrder($this->db);
        $this->product_order_detail  = new ProductOrderDetail($this->db);
        $this->product_image         = new ProductImage($this->db);
        $this->category              = new Category($this->db);
        $this->news_info             = new NewsInfo($this->db);
        $this->currency              = new Currency($this->db);
        $this->config                = new Config($this->db);
        $this->app_version           = new AppVersion($this->db);

        $this->client                = new CLIENT($this->db);
        $this->dashboard             = new DASHBOARD($this->db);
    }

    /*
     * ============================================================
     * ANDROID CLIENT API
     * ============================================================
     */
    private function info()                     { $this->client->info(); }
    private function listFeaturedNews()         { $this->client->findAllFeaturedNewsInfo(); }
    private function listProduct()              { $this->client->findAllProduct(); }
    private function getProductDetails()        { $this->client->findProductDetails(); }
    private function listCategory()             { $this->client->findAllCategory(); }
    private function listNews()                 { $this->client->findAllNewsInfo(); }
    private function getNewsDetails()           { $this->client->findNewsDetails(); }
    private function submitProductOrder()       { $this->client->submitProductOrder(); }

    /*
     * ============================================================
     * DASHBOARD API
     * ============================================================
     */
    private function getDashboardProduct()      { $this->dashboard->findDashboardProductData(); }
    private function getDashboardOthers()       { $this->dashboard->findDashboardOthersData(); }

    /*
     * ============================================================
     * PRODUCT TABLE
     * ============================================================
     */
    private function getOneProduct()            { $this->product->findOne(); }
    private function getAllProduct()            { $this->product->findAll(); }
    private function getAllProductByPage()      { $this->product->findAllByPage(); }
    private function getAllProductCount()       { $this->product->allCount(); }

    private function insertOneProduct() {
        $this->user->checkAuthorization();
        $this->product->insertOne();
    }

    private function updateOneProduct() {
        $this->user->checkAuthorization();
        $this->product->updateOne();
    }

    private function deleteOneProduct() {
        $this->user->checkAuthorization();
        $this->product->deleteOne();
    }

    /*
     * ============================================================
     * PRODUCT_CATEGORY TABLE
     * ============================================================
     */
    private function getAllProductCategory()    { $this->product_category->findAll(); }

    private function insertAllProductCategory() {
        $this->user->checkAuthorization();
        $this->product_category->deleteInsertAll();
    }

    /*
     * ============================================================
     * PRODUCT ORDER TABLE
     * ============================================================
     */
    private function getOneProductOrder()       { $this->product_order->findOne(); }
    private function getAllProductOrder()       { $this->product_order->findAll(); }
    private function getAllProductOrderByPage() { $this->product_order->findAllByPage(); }
    private function getAllProductOrderCount()  { $this->product_order->allCount(); }

    private function insertOneProductOrder() {
        $this->user->checkAuthorization();
        $this->product_order->insertOne();
    }

    private function updateOneProductOrder() {
        $this->user->checkAuthorization();
        $this->product_order->updateOne();
    }

    private function deleteOneProductOrder() {
        $this->user->checkAuthorization();
        $this->product_order->deleteOne();
    }

    private function processProductOrder() {
        $this->user->checkAuthorization();
        $this->product_order->processOrder();
    }

    /*
     * ============================================================
     * PRODUCT_ORDER_DETAIL TABLE
     * ============================================================
     */
    private function getAllProductOrderDetail()          { $this->product_order_detail->findAll(); }
    private function getAllProductOrderDetailByOrderId() { $this->product_order_detail->findAllByOrderId(); }

    private function insertAllProductOrderDetail() {
        $this->user->checkAuthorization();
        $this->product_order_detail->deleteInsertAll();
    }

    /*
     * ============================================================
     * PRODUCT IMAGE TABLE
     * ============================================================
     */
    private function getAllProductImageByProductId() { $this->product_image->findAllByProductId(); }
    private function getAllProductImage()            { $this->product_image->findAll(); }

    private function insertAllProductImage() {
        $this->user->checkAuthorization();
        $this->product_image->insertAll();
    }

    private function deleteProductImageByName() {
        $this->user->checkAuthorization();
        $this->product_image->delete();
    }

    /*
     * ============================================================
     * CATEGORY TABLE
     * ============================================================
     */
    private function getOneCategory()               { $this->category->findOne(); }
    private function getAllCategory()               { $this->category->findAll(); }
    private function getAllCategoryByPage()         { $this->category->findAllByPage(); }
    private function getAllCategoryCount()          { $this->category->allCount(); }
    private function getAllCategoryByProductId()    { $this->category->getAllByProductId(); }

    private function insertOneCategory() {
        $this->user->checkAuthorization();
        $this->category->insertOne();
    }

    private function updateOneCategory() {
        $this->user->checkAuthorization();
        $this->category->updateOne();
    }

    private function deleteOneCategory() {
        $this->user->checkAuthorization();
        $this->category->deleteOne();
    }

    /*
     * ============================================================
     * USERS TABLE
     * ============================================================
     */
    private function login()            { $this->user->processLogin(); }
    private function getOneUser()       { $this->user->findOne(); }

    private function updateOneUser() {
        $this->user->checkAuthorization();
        $this->user->updateOne();
    }

    private function insertOneUser() {
        $this->user->checkAuthorization();
        $this->user->insertOne();
    }

    /*
     * ============================================================
     * FCM TABLE
     * ============================================================
     */
    private function getAllFcm()        { $this->fcm->findAll(); }
    private function getAllFcmByPage()  { $this->fcm->findAllByPage(); }
    private function getAllFcmCount()   { $this->fcm->allCount(); }

    private function insertOneFcm()     { $this->fcm->insertOne(); }

    private function sendNotif() {
        $this->user->checkAuthorization();
        $this->fcm->processNotification();
    }

    /*
     * ============================================================
     * NEWS_INFO TABLE
     * ============================================================
     */
    private function getOneNewsInfo()       { $this->news_info->findOne(); }
    private function getAllNewsInfo()       { $this->news_info->findAll(); }
    private function getAllNewsInfoByPage() { $this->news_info->findAllByPage(); }
    private function getAllNewsInfoCount()  { $this->news_info->allCount(); }

    private function insertOneNewsInfo() {
        $this->user->checkAuthorization();
        $this->news_info->insertOne();
    }

    private function updateOneNewsInfo() {
        $this->user->checkAuthorization();
        $this->news_info->updateOne();
    }

    private function deleteOneNewsInfo() {
        $this->user->checkAuthorization();
        $this->news_info->deleteOne();
    }

    private function isFeaturedNewsExceed() {
        $this->news_info->isFeaturedNewsExceed();
    }

    /*
     * ============================================================
     * CURRENCY TABLE
     * ============================================================
     */
    private function getAllCurrency() { $this->currency->findAll(); }

    /*
     * ============================================================
     * APP_VERSION TABLE
     * ============================================================
     */
    private function getOneAppVersion()         { $this->app_version->findOne(); }
    private function getAllAppVersionByPage()   { $this->app_version->findAllByPage(); }
    private function getAllAppVersionCount()    { $this->app_version->allCount(); }

    private function insertOneAppVersion() {
        $this->user->checkAuthorization();
        $this->app_version->insertOne();
    }

    private function updateOneAppVersion() {
        $this->user->checkAuthorization();
        $this->app_version->updateOne();
    }

    private function deleteOneAppVersion() {
        $this->user->checkAuthorization();
        $this->app_version->deleteOne();
    }

    /*
     * ============================================================
     * CONFIG TABLE
     * ============================================================
     */
    private function getAllConfig() {
        $this->user->checkAuthorization();
        $this->config->findAll();
    }

    private function updateAllConfig() {
        $this->user->checkAuthorization();
        $this->config->updateAll();
    }

    /*
     * ============================================================
     * EMAIL
     * ============================================================
     */
    private function sendEmail() {
        $this->user->checkAuthorization();
        $this->mail->restEmail();
    }

    /*
     * ============================================================
     * CHECK DB CONNECTION
     * ============================================================
     */
    public function checkResponse() {
        $this->db->checkResponse_Impl();
    }

    /*
     * ============================================================
     * MAIN ROUTER
     * ============================================================
     */
    /*
    public function processApi() {

        $func = $_REQUEST['x'] ?? null;

        if (!$func) {
            echo 'processApi - method not exist';
            exit;
        }

        $func = strtolower(trim(str_replace("/", "", $func)));

        if (method_exists($this, $func)) {
            $this->$func();
        } else {
            echo 'processApi - method not exist';
            exit;
        }
    }
    */
    public function processApi() {

        // Obtener la acción solicitada
        $func = $_REQUEST['x'] ?? null;

        if (!$func) {
            echo 'processApi - method not exist';
            exit;
        }

        /*
         * Normalización de la ruta:
         * Ejemplos válidos:
         *   services/getAllProductOrderDetailByOrderId
         *   getAllProductOrderDetailByOrderId
         *
         * Resultado final:
         *   getAllProductOrderDetailByOrderId
         */
        $func = trim($func, '/');

        if (strpos($func, '/') !== false) {
            $func = basename($func);
        }

        // Seguridad básica: solo métodos existentes
        if (method_exists($this, $func)) {
            $this->$func();
        } else {
            echo 'processApi - method not exist';
            exit;
        }
    }


}

// Start API
$api = new API();
$api->processApi();
?>
