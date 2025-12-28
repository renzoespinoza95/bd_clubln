<?php
declare(strict_types=1);

require_once realpath(dirname(__FILE__) . "/tools/rest.php");

/*
 * This class handles all dashboard data
 */
class DASHBOARD extends REST {

    private ?mysqli $mysqli = null;
    private $db = null;

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

    public function __construct($db) {
        parent::__construct();
        $this->db = $db;
        $this->mysqli = $db->mysqli;

        // Instancias de modelos
        $this->user = new User($this->db);
        $this->product = new Product($this->db);
        $this->product_category = new ProductCategory($this->db);
        $this->product_order = new ProductOrder($this->db);
        $this->product_order_detail = new ProductOrderDetail($this->db);
        $this->product_image = new ProductImage($this->db);
        $this->category = new Category($this->db);
        $this->news_info = new NewsInfo($this->db);
        $this->currency = new Currency($this->db);
        $this->config = new Config($this->db);
        $this->app_version = new AppVersion($this->db);
    }

    /* ============================================================
       📌 DATOS PRINCIPALES DEL DASHBOARD (productos, órdenes, categorías)
       ============================================================ */
    public function findDashboardProductData(): void {

        $order = [
            'waiting'   => 0,
            'processed' => 0,
            'total'     => 0
        ];

        $product = [
            'published'    => 0,
            'draft'        => 0,
            'ready_stock'  => 0,
            'out_of_stock' => 0,
            'suspend'      => 0
        ];

        $category = [
            'published' => 0,
            'draft'     => 0
        ];

        // Orders
        $order['waiting']   = $this->product_order->countByStatusPlain('WAITING');
        $order['processed'] = $this->product_order->countByStatusPlain('PROCESSED');
        $order['total']     = $order['waiting'] + $order['processed'];

        // Products
        $product['published']    = $this->product->countByDraftPlain(0);
        $product['draft']        = $this->product->countByDraftPlain(1);
        $product['ready_stock']  = $this->product->countByStatusPlain('READY STOCK');
        $product['out_of_stock'] = $this->product->countByStatusPlain('OUT OF STOCK');
        $product['suspend']      = $this->product->countByStatusPlain('SUSPEND');

        // Categories
        $category['published'] = $this->category->countByDraftPlain(0);
        $category['draft']     = $this->category->countByDraftPlain(1);

        // Response
        $data = [
            'order'    => $order,
            'product'  => $product,
            'category' => $category
        ];

        $this->show_response($data);
    }

    /* ============================================================
       📌 OTROS DATOS DEL DASHBOARD (app, settings, noticias…)
       ============================================================ */
    public function findDashboardOthersData(): void {

        $news = [
            'featured'  => 0,
            'published' => 0,
            'draft'     => 0
        ];

        $app = [
            'active'   => 0,
            'inactive' => 0
        ];

        $setting = [
            'currency'      => "",
            'tax'           => 0,
            'featured_news' => 0
        ];

        $notification = [
            'users' => 0
        ];

        // Configuración general
        $setting_result = $this->config->findAllPlain();

        // News
        $news['featured']  = $this->news_info->countFeaturedPlain();
        $news['published'] = $this->news_info->countByDraftPlain(0);
        $news['draft']     = $this->news_info->countByDraftPlain(1);

        // App Versions
        $app['inactive'] = $this->app_version->countInactiveVersion();
        $app['active']   = $this->app_version->countActiveVersion();

        // Settings
        $setting['currency']      = $this->getValue($setting_result, 'CURRENCY');
        $setting['tax']           = $this->getValue($setting_result, 'TAX');
        $setting['featured_news'] = $this->getValue($setting_result, 'FEATURED_NEWS');

        // FCM notifications
        $notification['users'] = $this->fcm->allCountPlain();

        $data = [
            'news'         => $news,
            'app'          => $app,
            'setting'      => $setting,
            'notification' => $notification
        ];

        $this->show_response($data);
    }

    /* ============================================================
       📌 Obtener valor de un setting por código
       ============================================================ */
    private function getValue(array $data, string $code) {

        foreach ($data as $d) {
            if (($d['code'] ?? null) === $code) {
                return $d['value'] ?? null;
            }
        }

        return null; // Prevención de warnings en PHP 8
    }
}
?>
