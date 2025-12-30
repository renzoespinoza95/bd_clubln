<?php
require_once __DIR__ . '/inc/config.inc.php';

class CONF {

    /* Flag for demo version */
    public $DEMO_VERSION = false;

    /* Data configuration for database */
    public string $DB_SERVER;
    public string $DB_USER;
    public string $DB_PASSWORD;
    public string $DB_NAME;

    /* FCM key for notification */
    public $FCM_KEY     = "AIzaSyCv-90mFpx3SCWlIKSXXXXXXXXXXXXXXXXX";


    /* [ IMPORTANT ] be careful when edit this security code, use AlphaNumeric only*/
    /* This string must be same with security code at Android, if its different android unable to submit order */
    public $SECURITY_CODE = "8V06LupAaMBLtQqyqTxmcN42nn27FlejvaoSM3zXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

    /* Mailer config ---------------------------------------------------- */

    // change with yours
    public $SMTP_EMAIL      = "sample@your-domain.com";
    public $SMTP_PASSWORD   = "password";
    public $SMTP_HOST       = "mail.your-domain.com";
    public $SMTP_PORT       = 562;

    // for administrator & for buyer
    public $SUBJECT_EMAIL_NEW_ORDER = "Market New Order";
    public $TITLE_REPORT_NEW_ORDER  = "Market New Order";

    // for buyer
    public $SUBJECT_EMAIL_ORDER_PROCESSED   = "Order PROCESSED";
    public $TITLE_REPORT_ORDER_PROCESSED    = "Order Status Change to PROCESSED";

    public $SUBJECT_EMAIL_ORDER_UPDATED     = "Order Data Updated";
    public $TITLE_REPORT_ORDER_UPDATED      = "Order Data Updated By Admin";


    public function __construct() {
        // Variables vienen de config.inc.php
        $this->DB_SERVER   = $GLOBALS['host'];
        $this->DB_USER     = $GLOBALS['username'];
        $this->DB_PASSWORD = $GLOBALS['password'];
        $this->DB_NAME     = $GLOBALS['dbname'];

        $this->API_KEY     = $GLOBALS['apiKey'];
        $this->JWT_SECRET  = $GLOBALS['secreto_jwt'];
        $this->APP_NAME    = $GLOBALS['nombre_app'];

        $this->APP_HOST    = $GLOBALS['apphost'];
        $this->URL_FLASK   = $GLOBALS['url_flask'];
    }
}

?>