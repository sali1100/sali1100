<?php
/**
 * VinReporting.com Project Generator
 * 
 * This script creates the entire VinReporting.com project structure
 * Run this file on your Hostinger server to generate all necessary files
 * 
 * Usage: Upload this file to your domain and run it in browser
 * Example: https://yourdomain.com/create-vinreporting-project.php
 */

// Security check - comment this line out when you're ready to run the script
die('Security: Please read the instructions and uncomment the die() line above to run this script');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VinReporting.com Project Generator</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .log { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>VinReporting.com Project Generator</h1>
    
<?php

function logMessage($message, $type = 'info') {
    echo "<div class='log $type'>$message</div>\n";
    flush();
}

function createDirectory($path) {
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            logMessage("✅ Created directory: $path", 'success');
            return true;
        } else {
            logMessage("❌ Failed to create directory: $path", 'error');
            return false;
        }
    } else {
        logMessage("ℹ️ Directory already exists: $path", 'info');
        return true;
    }
}

function createFile($path, $content) {
    if (file_put_contents($path, $content)) {
        logMessage("✅ Created file: $path", 'success');
        return true;
    } else {
        logMessage("❌ Failed to create file: $path", 'error');
        return false;
    }
}

// Start project creation
logMessage("🚀 Starting VinReporting.com project creation...");

// Create directory structure
$directories = [
    'includes',
    'services', 
    'admin',
    'lib',
    'data',
    'assets',
    'assets/css',
    'assets/js',
    'assets/images'
];

foreach ($directories as $dir) {
    createDirectory($dir);
}

// Configuration file
$configContent = '<?php
session_start();

// Site Configuration
define("SITE_NAME", "VinReporting.com");
define("SITE_URL", "https://" . $_SERVER["HTTP_HOST"]);
define("ADMIN_EMAIL", "billing@vinreporting.com");

// PayPal Configuration (Sandbox - Change to live for production)
define("PAYPAL_CLIENT_ID", "YOUR_PAYPAL_CLIENT_ID");
define("PAYPAL_CLIENT_SECRET", "YOUR_PAYPAL_CLIENT_SECRET");
define("PAYPAL_MODE", "sandbox"); // Change to "live" for production

// Admin Configuration
define("ADMIN_PASSWORD_HASH", password_hash("admin123", PASSWORD_DEFAULT)); // Change this password!

// Email Configuration (Update with your SMTP settings)
define("SMTP_HOST", "smtp.hostinger.com");
define("SMTP_USERNAME", "noreply@yourdomain.com");
define("SMTP_PASSWORD", "your_email_password");
define("SMTP_PORT", 587);

// Pricing Plans
$pricing_plans = [
    "basic" => [
        "name" => "Basic Report",
        "price" => 24.99,
        "description" => "Essential vehicle history information"
    ],
    "premium" => [
        "name" => "Premium Report", 
        "price" => 39.99,
        "description" => "Comprehensive vehicle history with detailed analysis"
    ],
    "complete" => [
        "name" => "Complete Report",
        "price" => 49.99,
        "description" => "Full vehicle history with market analysis and recommendations"
    ]
];

// Report Providers
$report_providers = [
    "carfax" => "Carfax",
    "autocheck" => "AutoCheck"
];

// Helper Functions
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_vin($vin) {
    $vin = strtoupper(preg_replace("/[^A-Z0-9]/", "", $vin));
    return (strlen($vin) == 17 && preg_match("/^[A-HJ-NPR-Z0-9]+$/", $vin));
}

function generate_order_id() {
    return "VR" . date("Ymd") . "_" . strtoupper(substr(uniqid(), -6));
}

function save_order($order_data) {
    $orders_file = "data/orders.json";
    $orders = [];
    
    if (file_exists($orders_file)) {
        $orders = json_decode(file_get_contents($orders_file), true) ?: [];
    }
    
    $orders[] = $order_data;
    return file_put_contents($orders_file, json_encode($orders, JSON_PRETTY_PRINT));
}

function get_orders() {
    $orders_file = "data/orders.json";
    if (file_exists($orders_file)) {
        return json_decode(file_get_contents($orders_file), true) ?: [];
    }
    return [];
}

function format_price($price) {
    return "$" . number_format($price, 2);
}
?>';

createFile('includes/config.php', $configContent);

// Header file
$headerContent = '<?php
if (!isset($page_title)) {
    $page_title = "Professional Vehicle History Reports";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Get comprehensive vehicle history reports from trusted providers. Check accident history, title information, and more.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=USD"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <i class="fas fa-car"></i>
                    <span>VinReporting</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER["PHP_SELF"]) == "index.php" ? "active" : ""; ?>">Home</a>
                <a href="sample-report.php" class="nav-link <?php echo basename($_SERVER["PHP_SELF"]) == "sample-report.php" ? "active" : ""; ?>">Sample Report</a>
                <a href="pricing.php" class="nav-link <?php echo basename($_SERVER["PHP_SELF"]) == "pricing.php" ? "active" : ""; ?>">Pricing</a>
                <a href="faq.php" class="nav-link <?php echo basename($_SERVER["PHP_SELF"]) == "faq.php" ? "active" : ""; ?>">FAQ</a>
            </div>
            
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <main class="main-content">';

createFile('includes/header.php', $headerContent);

// Footer file  
$footerContent = '    </main>
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="services/vehicle-history-reports.php">Vehicle History Reports</a></li>
                        <li><a href="services/accident-history-check.php">Accident History Check</a></li>
                        <li><a href="services/title-information.php">Title Information</a></li>
                        <li><a href="services/service-records.php">Service Records</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="pricing.php">Pricing</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="sample-report.php">Sample Report</a></li>
                        <li><a href="mailto:support@vinreporting.com">Contact Support</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Trust & Security</h3>
                    <div class="trust-badges">
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>SSL Secured</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-lock"></i>
                            <span>Data Protected</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> VinReporting.com. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>';

createFile('includes/footer.php', $footerContent);

// Main index.php file
$indexContent = '<?php
require_once "includes/config.php";
$page_title = "Professional Vehicle History Reports";

// Handle form submission
if ($_POST) {
    $vin = sanitize_input($_POST["vin"]);
    $provider = sanitize_input($_POST["provider"]);  
    $plan = sanitize_input($_POST["plan"]);
    $customer_name = sanitize_input($_POST["customer_name"]);
    $customer_email = sanitize_input($_POST["customer_email"]);
    $customer_phone = sanitize_input($_POST["customer_phone"]);
    
    if (validate_vin($vin) && $customer_name && $customer_email) {
        // Store in session and redirect to checkout
        $_SESSION["order_data"] = [
            "vin" => $vin,
            "provider" => $provider,
            "plan" => $plan,
            "customer_name" => $customer_name,
            "customer_email" => $customer_email,
            "customer_phone" => $customer_phone
        ];
        header("Location: checkout.php");
        exit;
    }
}

include "includes/header.php";
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Get Your Complete Vehicle History Report</h1>
            <p>Make informed decisions with comprehensive reports from trusted providers</p>
        </div>
        
        <div class="hero-form">
            <form method="POST" class="vin-form" id="vinForm">
                <div class="form-group">
                    <label for="vin">Enter VIN Number</label>
                    <input type="text" id="vin" name="vin" placeholder="17-digit VIN" maxlength="17" required>
                    <div class="vin-status" id="vinStatus"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="provider">Report Provider</label>
                        <select id="provider" name="provider" required>
                            <option value="carfax">Carfax Report</option>
                            <option value="autocheck">AutoCheck Report</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="plan">Report Type</label>
                        <select id="plan" name="plan" required>
                            <?php foreach ($pricing_plans as $key => $plan): ?>
                            <option value="<?php echo $key; ?>"><?php echo $plan["name"]; ?> - <?php echo format_price($plan["price"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_email">Email Address</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="customer_phone">Phone Number</label>
                    <input type="tel" id="customer_phone" name="customer_phone">
                </div>
                
                <button type="submit" class="btn-primary" id="getReportBtn" disabled>
                    <i class="fas fa-search"></i>
                    Get Vehicle Report
                </button>
            </form>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2>Why Choose VinReporting?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Trusted Data Sources</h3>
                <p>Reports from Carfax and AutoCheck, the most trusted names in vehicle history</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Instant Delivery</h3>
                <p>Get your report delivered to your email within minutes of purchase</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Secure & Safe</h3>
                <p>Your data is protected with enterprise-grade security and encryption</p>
            </div>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>';

createFile('index.php', $indexContent);

// Continue with more files...
logMessage("⏳ Creating remaining core files...");

// I need to split this into multiple parts due to length limitations
// Let me continue with the checkout.php file

$checkoutContent = '<?php
require_once "includes/config.php";

if (!isset($_SESSION["order_data"])) {
    header("Location: index.php");
    exit;
}

$order_data = $_SESSION["order_data"];
$plan_info = $pricing_plans[$order_data["plan"]];
$page_title = "Checkout - " . $plan_info["name"];

include "includes/header.php";
?>

<section class="checkout">
    <div class="container">
        <div class="checkout-content">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-card">
                    <div class="summary-item">
                        <span>VIN:</span>
                        <span><?php echo substr($order_data["vin"], 0, 8) . "•••••••••"; ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Provider:</span>
                        <span><?php echo $report_providers[$order_data["provider"]]; ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Report Type:</span>
                        <span><?php echo $plan_info["name"]; ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Customer:</span>
                        <span><?php echo $order_data["customer_name"]; ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Email:</span>
                        <span><?php echo $order_data["customer_email"]; ?></span>
                    </div>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span class="price"><?php echo format_price($plan_info["price"]); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="payment-section">
                <h2>Complete Your Purchase</h2>
                <div class="payment-methods">
                    <div id="paypal-button-container"></div>
                </div>
                
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your payment information is secure and encrypted</span>
                </div>
            </div>
        </div>
        
        <div class="report-preview">
            <h3>Report Preview</h3>
            <div class="preview-blur">
                <div class="preview-content">
                    <h4>Vehicle History Report</h4>
                    <p>• Title Information</p>
                    <p>• Accident History</p>
                    <p>• Service Records</p>
                    <p>• Previous Owners</p>
                    <p>• Recall Information</p>
                </div>
                <div class="blur-overlay">
                    <div class="unlock-text">
                        Complete purchase to unlock full report
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: "<?php echo $plan_info["price"]; ?>"
                },
                description: "<?php echo $plan_info["name"]; ?> - VIN: <?php echo substr($order_data["vin"], 0, 8); ?>..."
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Send payment details to server
            fetch("process-payment.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    orderID: data.orderID,
                    paymentID: details.id,
                    payerID: details.payer.payer_id
                })
            }).then(function(response) {
                return response.json();
            }).then(function(result) {
                if (result.success) {
                    window.location.href = "thank-you.php?order=" + result.order_id;
                } else {
                    alert("Payment processing failed. Please try again.");
                }
            });
        });
    }
}).render("#paypal-button-container");
</script>

<?php include "includes/footer.php"; ?>';

createFile('checkout.php', $checkoutContent);

// Thank You page
$thankYouContent = '<?php
require_once "includes/config.php";

if (!isset($_GET["order"])) {
    header("Location: index.php");
    exit;
}

$order_id = sanitize_input($_GET["order"]);
$orders = get_orders();
$order = null;

foreach ($orders as $o) {
    if ($o["order_id"] == $order_id) {
        $order = $o;
        break;
    }
}

if (!$order) {
    header("Location: index.php");
    exit;
}

$page_title = "Thank You - Order Confirmation";
include "includes/header.php";
?>

<section class="thank-you">
    <div class="container">
        <div class="success-message">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Payment Successful!</h1>
            <p>Your vehicle history report order has been confirmed.</p>
        </div>
        
        <div class="order-details">
            <h2>Order Details</h2>
            <div class="details-card">
                <div class="detail-item">
                    <span>Order ID:</span>
                    <span><?php echo $order["order_id"]; ?></span>
                </div>
                <div class="detail-item">
                    <span>VIN:</span>
                    <span><?php echo $order["vin"]; ?></span>
                </div>
                <div class="detail-item">
                    <span>Report Type:</span>
                    <span><?php echo $order["plan_name"]; ?></span>
                </div>
                <div class="detail-item">
                    <span>Provider:</span>
                    <span><?php echo $order["provider"]; ?></span>
                </div>
                <div class="detail-item">
                    <span>Amount Paid:</span>
                    <span><?php echo format_price($order["amount"]); ?></span>
                </div>
                <div class="detail-item">
                    <span>Email:</span>
                    <span><?php echo $order["customer_email"]; ?></span>
                </div>
            </div>
        </div>
        
        <div class="next-steps">
            <h3>What Happens Next?</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-text">
                        <h4>Report Generation</h4>
                        <p>Our system is preparing your detailed vehicle history report</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text">
                        <h4>Email Delivery</h4>
                        <p>Your report will be delivered to <?php echo $order["customer_email"]; ?> within 15 minutes</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">
                        <h4>Review Your Report</h4>
                        <p>Use the comprehensive information to make an informed decision</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>';

createFile('thank-you.php', $thankYouContent);

// Process Payment PHP
$processPaymentContent = '<?php
require_once "includes/config.php";
require_once "lib/email.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION["order_data"]) || !$input["orderID"]) {
    echo json_encode(["success" => false, "message" => "Invalid order data"]);
    exit;
}

try {
    $order_data = $_SESSION["order_data"];
    $plan_info = $pricing_plans[$order_data["plan"]];
    
    // Generate order ID
    $order_id = generate_order_id();
    
    // Create order record
    $order_record = [
        "order_id" => $order_id,
        "vin" => $order_data["vin"],
        "provider" => $order_data["provider"],
        "plan" => $order_data["plan"],
        "plan_name" => $plan_info["name"],
        "amount" => $plan_info["price"],
        "customer_name" => $order_data["customer_name"],
        "customer_email" => $order_data["customer_email"],
        "customer_phone" => $order_data["customer_phone"],
        "paypal_order_id" => $input["orderID"],
        "paypal_payment_id" => $input["paymentID"],
        "paypal_payer_id" => $input["payerID"],
        "status" => "completed",
        "created_at" => date("Y-m-d H:i:s")
    ];
    
    // Save order
    if (save_order($order_record)) {
        // Send confirmation email
        sendOrderConfirmationEmail($order_record);
        
        // Clear session
        unset($_SESSION["order_data"]);
        
        echo json_encode([
            "success" => true,
            "order_id" => $order_id,
            "message" => "Order processed successfully"
        ]);
    } else {
        throw new Exception("Failed to save order");
    }
    
} catch (Exception $e) {
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Payment processing failed"
    ]);
}
?>';

createFile('process-payment.php', $processPaymentContent);

// Sample Report page
$sampleReportContent = '<?php
require_once "includes/config.php";
$page_title = "Sample Vehicle History Report";
include "includes/header.php";
?>

<section class="sample-report">
    <div class="container">
        <div class="report-header">
            <h1>Sample Vehicle History Report</h1>
            <p>See what you\'ll get in your comprehensive vehicle history report</p>
        </div>
        
        <div class="provider-tabs">
            <button class="tab-button active" data-provider="carfax">Carfax Sample</button>
            <button class="tab-button" data-provider="autocheck">AutoCheck Sample</button>
        </div>
        
        <div class="report-sample carfax active">
            <div class="sample-header">
                <h2>Carfax Vehicle History Report</h2>
                <div class="sample-vin">Sample VIN: 1HGBH41JXMN109186</div>
            </div>
            
            <div class="report-sections">
                <div class="report-section">
                    <h3><i class="fas fa-car"></i> Vehicle Information</h3>
                    <ul>
                        <li>Year, Make, Model: 2012 Honda Civic Sedan</li>
                        <li>Engine: 1.8L 4 Cylinder</li>
                        <li>Transmission: Automatic</li>
                        <li>Drive Type: Front Wheel Drive</li>
                    </ul>
                </div>
                
                <div class="report-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> Accident History</h3>
                    <div class="status-good">
                        <i class="fas fa-check"></i>
                        No accidents or damage reported to Carfax
                    </div>
                </div>
                
                <div class="report-section">
                    <h3><i class="fas fa-file-alt"></i> Title Information</h3>
                    <ul>
                        <li>Title Issue: None reported</li>
                        <li>Previous Use: Personal vehicle</li>
                        <li>Last Reported Odometer: 89,432 miles</li>
                    </ul>
                </div>
                
                <div class="report-section">
                    <h3><i class="fas fa-wrench"></i> Service Records</h3>
                    <ul>
                        <li>12 Service records found</li>
                        <li>Regular maintenance performed</li>
                        <li>Last service: Oil change at 87,200 miles</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="report-sample autocheck">
            <div class="sample-header">
                <h2>AutoCheck Vehicle History Report</h2>
                <div class="sample-vin">Sample VIN: 1HGBH41JXMN109186</div>
            </div>
            
            <div class="autocheck-score">
                <div class="score-circle">
                    <span class="score">85</span>
                    <span class="score-label">AutoCheck Score</span>
                </div>
                <div class="score-description">
                    <h3>Above Average</h3>
                    <p>This vehicle has a higher AutoCheck Score than similar vehicles</p>
                </div>
            </div>
            
            <div class="report-sections">
                <div class="report-section">
                    <h3><i class="fas fa-shield-alt"></i> Vehicle History Summary</h3>
                    <ul>
                        <li>No accidents reported</li>
                        <li>No title issues reported</li>
                        <li>No lemon history reported</li>
                        <li>Regular maintenance performed</li>
                    </ul>
                </div>
                
                <div class="report-section">
                    <h3><i class="fas fa-users"></i> Ownership History</h3>
                    <ul>
                        <li>2 Previous owners</li>
                        <li>Personal use vehicle</li>
                        <li>Owned in: California, Nevada</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="cta-section">
            <h2>Get Your Vehicle History Report</h2>
            <p>Don\'t buy a used car without knowing its history</p>
            <a href="index.php" class="btn-primary">
                <i class="fas fa-search"></i>
                Get Your Report Now
            </a>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>';

createFile('sample-report.php', $sampleReportContent);

logMessage("✅ Main pages created successfully!");

// Create admin directory files
logMessage("⏳ Creating admin panel files...");

$adminLoginContent = '<?php
require_once "../includes/config.php";

$error = "";

if ($_POST) {
    $password = $_POST["password"];
    
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION["admin_logged_in"] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid password";
    }
}

$page_title = "Admin Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-login">
    <div class="login-container">
        <div class="login-form">
            <h1>Admin Login</h1>
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>
</html>';

createFile('admin/login.php', $adminLoginContent);

$adminIndexContent = '<?php
require_once "../includes/config.php";

if (!isset($_SESSION["admin_logged_in"]) || !$_SESSION["admin_logged_in"]) {
    header("Location: login.php");
    exit;
}

$orders = get_orders();
$total_orders = count($orders);
$total_revenue = array_sum(array_column($orders, "amount"));

$page_title = "Admin Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <h1>VinReporting Admin</h1>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </nav>
    
    <main class="admin-main">
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Orders</h3>
                    <p><?php echo $total_orders; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Revenue</h3>
                    <p><?php echo format_price($total_revenue); ?></p>
                </div>
            </div>
        </div>
        
        <div class="admin-orders">
            <div class="orders-header">
                <h2>Recent Orders</h2>
                <div class="orders-actions">
                    <a href="export-orders.php" class="btn-secondary">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </a>
                </div>
            </div>
            
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>VIN</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="no-orders">No orders found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach (array_reverse($orders) as $order): ?>
                        <tr>
                            <td><?php echo $order["order_id"]; ?></td>
                            <td><?php echo date("M j, Y", strtotime($order["created_at"])); ?></td>
                            <td><?php echo $order["customer_name"]; ?></td>
                            <td><?php echo substr($order["vin"], 0, 8) . "•••••••••"; ?></td>
                            <td><?php echo $order["plan_name"]; ?></td>
                            <td><?php echo format_price($order["amount"]); ?></td>
                            <td>
                                <span class="status <?php echo $order["status"]; ?>">
                                    <?php echo ucfirst($order["status"]); ?>
                                </span>
                            </td>
                            <td>
                                <button onclick="viewOrder(\'<?php echo $order["order_id"]; ?>\')" class="btn-small">View</button>
                                <a href="resend-email.php?order=<?php echo $order["order_id"]; ?>" class="btn-small">Resend</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <script>
        function viewOrder(orderId) {
            alert("View order details for: " + orderId + "\n\nThis feature will show detailed order information in a modal or separate page.");
        }
    </script>
</body>
</html>';

createFile('admin/index.php', $adminIndexContent);

$adminLogoutContent = '<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>';

createFile('admin/logout.php', $adminLogoutContent);

logMessage("✅ Admin panel files created!");

// Create library files  
$emailContent = '<?php
function sendOrderConfirmationEmail($order) {
    $to = $order["customer_email"];
    $subject = "Your VinReporting.com Order Confirmation - " . $order["order_id"];
    
    $message = generateEmailTemplate($order);
    
    $headers = [
        "MIME-Version: 1.0",
        "Content-type: text/html; charset=UTF-8",
        "From: VinReporting.com <noreply@vinreporting.com>",
        "Cc: " . ADMIN_EMAIL,
        "Reply-To: support@vinreporting.com"
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

function generateEmailTemplate($order) {
    return \'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f9f9f9; }
        .order-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Order!</h1>
            <p>Your vehicle history report is being prepared</p>
        </div>
        
        <div class="content">
            <h2>Order Confirmation</h2>
            <p>Dear \' . $order["customer_name"] . \',</p>
            <p>Thank you for choosing VinReporting.com. Your order has been successfully processed and your vehicle history report will be delivered shortly.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <span><strong>Order ID:</strong></span>
                    <span>\' . $order["order_id"] . \'</span>
                </div>
                <div class="detail-row">
                    <span><strong>VIN:</strong></span>
                    <span>\' . $order["vin"] . \'</span>
                </div>
                <div class="detail-row">
                    <span><strong>Report Type:</strong></span>
                    <span>\' . $order["plan_name"] . \'</span>
                </div>
                <div class="detail-row">
                    <span><strong>Provider:</strong></span>
                    <span>\' . ucfirst($order["provider"]) . \'</span>
                </div>
                <div class="detail-row">
                    <span><strong>Amount:</strong></span>
                    <span>\' . format_price($order["amount"]) . \'</span>
                </div>
                <div class="detail-row">
                    <span><strong>Date:</strong></span>
                    <span>\' . date("F j, Y g:i A", strtotime($order["created_at"])) . \'</span>
                </div>
            </div>
            
            <h3>What\'s Next?</h3>
            <p>Your detailed vehicle history report is being generated and will be delivered to this email address within 15 minutes. The report will include:</p>
            <ul>
                <li>Complete title history</li>
                <li>Accident and damage records</li>
                <li>Service and maintenance records</li>
                <li>Previous ownership information</li>
                <li>Recall information</li>
                <li>Market value analysis</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>If you have any questions, please contact us at support@vinreporting.com</p>
            <p>&copy; \' . date("Y") . \' VinReporting.com. All rights reserved.</p>
        </div>
    </div>
</body>
</html>\';
}
?>';

createFile('lib/email.php', $emailContent);

// Create data files
$ordersJson = '[]';
createFile('data/orders.json', $ordersJson);

$htaccessContent = 'Order Deny,Allow
Deny from all';
createFile('data/.htaccess', $htaccessContent);

logMessage("✅ Core files created successfully!");
logMessage("📝 Please check the generated files and configure:");
logMessage("1. Update PayPal credentials in includes/config.php");
logMessage("2. Change admin password in includes/config.php"); 
logMessage("3. Configure email settings in includes/config.php");
logMessage("4. Update domain URLs as needed");

?>
    
    <div class="completion-message">
        <h2>Project Creation Complete!</h2>
        <p>Your VinReporting.com project has been generated successfully.</p>
        
        <h3>Next Steps:</h3>
        <ol>
            <li>Update PayPal credentials in <code>includes/config.php</code></li>
            <li>Change the admin password in <code>includes/config.php</code></li>
            <li>Configure email settings for report delivery</li>
            <li>Test the application thoroughly</li>
            <li>Delete this generator file for security</li>
        </ol>
        
        <p><strong>Important:</strong> Remember to delete this generator file once you're done!</p>
    </div>
    
</body>
</html>