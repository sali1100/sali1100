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
    return '
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
            <p>Dear ' . $order["customer_name"] . ',</p>
            <p>Thank you for choosing VinReporting.com. Your order has been successfully processed and your vehicle history report will be delivered shortly.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <span><strong>Order ID:</strong></span>
                    <span>' . $order["order_id"] . '</span>
                </div>
                <div class="detail-row">
                    <span><strong>VIN:</strong></span>
                    <span>' . $order["vin"] . '</span>
                </div>
                <div class="detail-row">
                    <span><strong>Report Type:</strong></span>
                    <span>' . $order["plan_name"] . '</span>
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

// Create CSS file
$cssContent = '/* VinReporting.com - Main Stylesheet */

:root {
    --primary-color: #667eea;
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-color: #f093fb;
    --success-color: #10b981;
    --error-color: #ef4444;
    --warning-color: #f59e0b;
    --text-color: #1f2937;
    --text-light: #6b7280;
    --bg-color: #ffffff;
    --bg-light: #f9fafb;
    --border-color: #e5e7eb;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Inter", "Segoe UI", system-ui, sans-serif;
    line-height: 1.6;
    color: var(--text-color);
    background-color: var(--bg-color);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Navigation */
.navbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    transition: all 0.3s ease;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
}

.nav-logo a {
    display: flex;
    align-items: center;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.nav-logo i {
    margin-right: 0.5rem;
    font-size: 1.8rem;
}

.nav-menu {
    display: flex;
    gap: 2rem;
}

.nav-link {
    text-decoration: none;
    color: var(--text-color);
    font-weight: 500;
    transition: color 0.3s ease;
    position: relative;
}

.nav-link:hover,
.nav-link.active {
    color: var(--primary-color);
}

.nav-link.active::after {
    content: "";
    position: absolute;
    bottom: -5px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-gradient);
    border-radius: 1px;
}

.nav-toggle {
    display: none;
    cursor: pointer;
    font-size: 1.5rem;
}

/* Main Content */
.main-content {
    margin-top: 80px;
    min-height: calc(100vh - 80px);
}

/* Hero Section */
.hero {
    background: var(--primary-gradient);
    color: white;
    padding: 4rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 20px;
}

.hero-text h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-text p {
    font-size: 1.25rem;
    margin-bottom: 3rem;
    opacity: 0.9;
}

/* Form Styles */
.hero-form {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    max-width: 600px;
    margin: 0 auto;
}

.vin-form {
    color: var(--text-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.vin-status {
    font-size: 0.875rem;
    margin-top: 0.25rem;
    min-height: 20px;
}

.vin-status.valid {
    color: var(--success-color);
}

.vin-status.invalid {
    color: var(--error-color);
}

/* Buttons */
.btn-primary {
    background: var(--primary-gradient);
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-secondary {
    background: white;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: var(--primary-color);
    color: white;
}

.btn-small {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border: 1px solid var(--border-color);
    background: white;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 0.5rem;
}

/* Features Section */
.features {
    padding: 4rem 0;
    background: var(--bg-light);
}

.features h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--text-color);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.feature-card h3 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: var(--text-color);
}

/* Checkout Styles */
.checkout {
    padding: 2rem 0;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 2rem;
}

.order-summary h2,
.payment-section h2 {
    margin-bottom: 1.5rem;
    color: var(--text-color);
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0 0;
    font-weight: 600;
    font-size: 1.25rem;
    color: var(--primary-color);
}

.price {
    font-weight: 700;
}

#paypal-button-container {
    margin: 1rem 0;
}

.security-note {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-light);
    font-size: 0.875rem;
    margin-top: 1rem;
}

/* Report Preview */
.report-preview {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.preview-blur {
    position: relative;
}

.preview-content {
    padding: 1rem;
}

.blur-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.unlock-text {
    font-weight: 600;
    color: var(--primary-color);
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

/* Thank You Page */
.thank-you {
    padding: 3rem 0;
    text-align: center;
}

.success-message {
    margin-bottom: 3rem;
}

.success-icon {
    font-size: 4rem;
    color: var(--success-color);
    margin-bottom: 1rem;
}

.success-message h1 {
    color: var(--success-color);
    margin-bottom: 1rem;
}

.order-details,
.next-steps {
    max-width: 600px;
    margin: 0 auto 3rem;
    text-align: left;
}

.details-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    margin-top: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.steps {
    margin-top: 2rem;
}

.step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.step-number {
    background: var(--primary-gradient);
    color: white;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 1rem;
    flex-shrink: 0;
}

.step-text h4 {
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

/* Admin Styles */
.admin-login {
    background: var(--primary-gradient);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    width: 100%;
    max-width: 400px;
}

.admin-dashboard {
    background: var(--bg-light);
    min-height: 100vh;
}

.admin-nav {
    background: white;
    box-shadow: var(--shadow);
    padding: 1rem 0;
}

.admin-nav-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
}

.admin-nav h1 {
    color: var(--primary-color);
    font-size: 1.5rem;
}

.logout-btn {
    color: var(--error-color);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    background: var(--primary-gradient);
    color: white;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-info h3 {
    color: var(--text-light);
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.stat-info p {
    color: var(--text-color);
    font-size: 1.5rem;
    font-weight: 700;
}

.admin-orders {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.orders-table {
    overflow-x: auto;
}

.orders-table table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th,
.orders-table td {
    text-align: left;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.orders-table th {
    background: var(--bg-light);
    font-weight: 600;
    color: var(--text-color);
}

.status {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status.completed {
    background: #d1fae5;
    color: #065f46;
}

.no-orders {
    text-align: center;
    color: var(--text-light);
    font-style: italic;
}

/* Sample Report Styles */
.sample-report {
    padding: 2rem 0;
}

.report-header {
    text-align: center;
    margin-bottom: 3rem;
}

.provider-tabs {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.tab-button {
    padding: 0.75rem 1.5rem;
    border: 2px solid var(--border-color);
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.tab-button.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.report-sample {
    display: none;
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.report-sample.active {
    display: block;
}

.sample-header {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}

.sample-vin {
    color: var(--text-light);
    font-size: 0.875rem;
}

.report-sections {
    display: grid;
    gap: 1.5rem;
}

.report-section {
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.report-section h3 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.status-good {
    background: #d1fae5;
    color: #065f46;
    padding: 0.75rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.autocheck-score {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--bg-light);
    border-radius: 8px;
}

.score-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.score {
    font-size: 1.5rem;
    font-weight: 700;
}

.score-label {
    font-size: 0.75rem;
    opacity: 0.9;
}

.cta-section {
    text-align: center;
    padding: 3rem 2rem;
    background: var(--bg-light);
    border-radius: 12px;
    margin-top: 2rem;
}

/* Footer */
.footer {
    background: var(--text-color);
    color: white;
    padding: 3rem 0 1rem;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h3 {
    margin-bottom: 1rem;
    color: white;
}

.footer-section ul {
    list-style: none;
}

.footer-section li {
    margin-bottom: 0.5rem;
}

.footer-section a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section a:hover {
    color: white;
}

.trust-badges {
    display: flex;
    gap: 1rem;
}

.trust-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.footer-bottom {
    border-top: 1px solid #374151;
    padding-top: 1rem;
    text-align: center;
    color: #9ca3af;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-menu {
        display: none;
    }
    
    .nav-toggle {
        display: block;
    }
    
    .hero-text h1 {
        font-size: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .checkout-content {
        grid-template-columns: 1fr;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .trust-badges {
        flex-direction: column;
    }
}

/* Error Messages */
.error-message {
    background: #fee2e2;
    color: #991b1b;
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    border: 1px solid #fecaca;
}

/* Animation Classes */
.fade-in {
    opacity: 0;
    animation: fadeIn 0.6s ease forwards;
}

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}

.slide-up {
    transform: translateY(20px);
    opacity: 0;
    animation: slideUp 0.6s ease forwards;
}

@keyframes slideUp {
    to {
        transform: translateY(0);
        opacity: 1;
    }
}';

createFile('assets/css/style.css', $cssContent);

// Create JavaScript file
$jsContent = '// VinReporting.com - Main JavaScript

document.addEventListener("DOMContentLoaded", function() {
    // VIN Validation
    const vinInput = document.getElementById("vin");
    const vinStatus = document.getElementById("vinStatus");
    const getReportBtn = document.getElementById("getReportBtn");
    
    if (vinInput) {
        vinInput.addEventListener("input", validateVIN);
        vinInput.addEventListener("paste", function(e) {
            setTimeout(validateVIN, 10);
        });
    }
    
    function validateVIN() {
        const vin = vinInput.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
        vinInput.value = vin;
        
        if (vin.length === 0) {
            vinStatus.textContent = "";
            vinStatus.className = "vin-status";
            updateSubmitButton();
            return;
        }
        
        if (vin.length < 17) {
            vinStatus.textContent = `Enter ${17 - vin.length} more characters`;
            vinStatus.className = "vin-status invalid";
            updateSubmitButton();
            return;
        }
        
        if (vin.length === 17) {
            // Check for invalid characters (I, O, Q not allowed in VIN)
            if (/[IOQ]/.test(vin)) {
                vinStatus.textContent = "Invalid VIN: Letters I, O, Q are not allowed";
                vinStatus.className = "vin-status invalid";
                updateSubmitButton();
                return;
            }
            
            vinStatus.textContent = "✓ Valid VIN format";
            vinStatus.className = "vin-status valid";
            updateSubmitButton();
            return;
        }
        
        if (vin.length > 17) {
            vinStatus.textContent = "VIN must be exactly 17 characters";
            vinStatus.className = "vin-status invalid";
            updateSubmitButton();
        }
    }
    
    function updateSubmitButton() {
        if (!getReportBtn) return;
        
        const vin = vinInput.value.replace(/[^A-Z0-9]/g, "");
        const customerName = document.getElementById("customer_name")?.value.trim();
        const customerEmail = document.getElementById("customer_email")?.value.trim();
        
        const isValidVIN = vin.length === 17 && !/[IOQ]/.test(vin);
        const hasCustomerInfo = customerName && customerEmail;
        
        getReportBtn.disabled = !(isValidVIN && hasCustomerInfo);
    }
    
    // Update submit button when customer info changes
    const customerInputs = ["customer_name", "customer_email"];
    customerInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", updateSubmitButton);
        }
    });
    
    // Mobile Navigation
    const navToggle = document.querySelector(".nav-toggle");
    const navMenu = document.querySelector(".nav-menu");
    
    if (navToggle && navMenu) {
        navToggle.addEventListener("click", function() {
            navMenu.classList.toggle("active");
            
            // Toggle hamburger icon
            const icon = navToggle.querySelector("i");
            if (icon) {
                icon.classList.toggle("fa-bars");
                icon.classList.toggle("fa-times");
            }
        });
    }
    
    // Sample Report Tabs
    const tabButtons = document.querySelectorAll(".tab-button");
    const reportSamples = document.querySelectorAll(".report-sample");
    
    tabButtons.forEach(button => {
        button.addEventListener("click", function() {
            const provider = this.dataset.provider;
            
            // Update active tab
            tabButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
            
            // Update active report sample
            reportSamples.forEach(sample => {
                sample.classList.remove("active");
                if (sample.classList.contains(provider)) {
                    sample.classList.add("active");
                }
            });
        });
    });
    
    // Scroll Animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("fade-in");
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll(".feature-card, .step, .report-section").forEach(el => {
        observer.observe(el);
    });
    
    // Form Validation Enhancement
    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", function(e) {
            const requiredInputs = form.querySelectorAll("[required]");
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = "var(--error-color)";
                    
                    // Remove error styling after user starts typing
                    input.addEventListener("input", function() {
                        this.style.borderColor = "";
                    }, { once: true });
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert("Please fill in all required fields");
            }
        });
    });
    
    // Smooth Scrolling for Anchor Links
    document.querySelectorAll(\'a[href^="#"]\').forEach(anchor => {
        anchor.addEventListener("click", function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });
    
    // Auto-format Phone Number
    const phoneInput = document.getElementById("customer_phone");
    if (phoneInput) {
        phoneInput.addEventListener("input", function(e) {
            let value = e.target.value.replace(/\D/g, "");
            
            if (value.length >= 6) {
                value = `(${value.slice(0,3)}) ${value.slice(3,6)}-${value.slice(6,10)}`;
            } else if (value.length >= 3) {
                value = `(${value.slice(0,3)}) ${value.slice(3)}`;
            }
            
            e.target.value = value;
        });
    }
    
    // Loading States
    function showLoading(element) {
        const originalText = element.textContent;
        element.textContent = "Processing...";
        element.disabled = true;
        
        // Store original text for restoration
        element.dataset.originalText = originalText;
    }
    
    function hideLoading(element) {
        element.textContent = element.dataset.originalText || "Submit";
        element.disabled = false;
    }
    
    // Add loading state to forms
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function() {
            const submitBtn = form.querySelector(\'button[type="submit"]\');
            if (submitBtn) {
                showLoading(submitBtn);
            }
        });
    });
    
    // Navbar scroll effect
    let lastScrollTop = 0;
    const navbar = document.querySelector(".navbar");
    
    window.addEventListener("scroll", function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            navbar.style.transform = "translateY(-100%)";
        } else {
            // Scrolling up
            navbar.style.transform = "translateY(0)";
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Add subtle animations on page load
    setTimeout(() => {
        document.querySelectorAll(".hero-text, .hero-form").forEach((el, index) => {
            el.style.opacity = "0";
            el.style.transform = "translateY(30px)";
            el.style.transition = "all 0.8s ease";
            
            setTimeout(() => {
                el.style.opacity = "1";
                el.style.transform = "translateY(0)";
            }, index * 200);
        });
    }, 100);
});

// Utility Functions
function formatPrice(price) {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD"
    }).format(price);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric"
    });
}

// Export for global access
window.VinReporting = {
    formatPrice,
    formatDate
};';

createFile('assets/js/main.js', $jsContent);

// Create remaining pages
$pricingContent = '<?php
require_once "includes/config.php";
$page_title = "Pricing Plans - Vehicle History Reports";
include "includes/header.php";
?>

<section class="pricing">
    <div class="container">
        <div class="pricing-header">
            <h1>Choose Your Report Plan</h1>
            <p>Select the perfect plan for your vehicle research needs</p>
        </div>
        
        <div class="pricing-grid">
            <?php foreach ($pricing_plans as $key => $plan): ?>
            <div class="pricing-card <?php echo $key === "premium" ? "featured" : ""; ?>">
                <?php if ($key === "premium"): ?>
                    <div class="featured-badge">Most Popular</div>
                <?php endif; ?>
                
                <h3><?php echo $plan["name"]; ?></h3>
                <div class="price">
                    <span class="currency">$</span>
                    <span class="amount"><?php echo number_format($plan["price"], 0); ?></span>
                    <span class="period">.<?php echo substr(number_format($plan["price"], 2), -2); ?></span>
                </div>
                <p class="description"><?php echo $plan["description"]; ?></p>
                
                <ul class="features">
                    <li><i class="fas fa-check"></i> Complete Vehicle History</li>
                    <li><i class="fas fa-check"></i> Accident & Damage Records</li>
                    <li><i class="fas fa-check"></i> Title Information</li>
                    <li><i class="fas fa-check"></i> Service Records</li>
                    <?php if ($key !== "basic"): ?>
                    <li><i class="fas fa-check"></i> Previous Owners Details</li>
                    <li><i class="fas fa-check"></i> Recall Information</li>
                    <?php endif; ?>
                    <?php if ($key === "complete"): ?>
                    <li><i class="fas fa-check"></i> Market Value Analysis</li>
                    <li><i class="fas fa-check"></i> Detailed Recommendations</li>
                    <?php endif; ?>
                </ul>
                
                <a href="index.php#order-form" class="btn-primary">
                    Get <?php echo $plan["name"]; ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>';

createFile('pricing.php', $pricingContent);

$faqContent = '<?php
require_once "includes/config.php";
$page_title = "Frequently Asked Questions";
include "includes/header.php";
?>

<section class="faq">
    <div class="container">
        <div class="faq-header">
            <h1>Frequently Asked Questions</h1>
            <p>Get answers to common questions about our vehicle history reports</p>
        </div>
        
        <div class="faq-content">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What information is included in a vehicle history report?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Our comprehensive vehicle history reports include accident history, title information, service records, previous ownership details, recall information, and market value analysis. The exact details depend on the report plan you choose.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How quickly will I receive my report?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Most reports are delivered within 15 minutes of purchase completion. You\'ll receive your detailed report via email at the address provided during checkout.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What\'s the difference between Carfax and AutoCheck reports?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Both providers offer comprehensive vehicle history information. Carfax is known for detailed service records, while AutoCheck provides an easy-to-understand scoring system. Both are trusted industry leaders with extensive databases.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Is my payment information secure?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Yes, we use PayPal\'s secure payment processing system. Your financial information is encrypted and never stored on our servers. We maintain the highest security standards to protect your data.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What if no history is found for my VIN?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Even if limited history is available, you\'ll receive a report showing what information is accessible. This is valuable information itself, as it may indicate the vehicle\'s history is clean or that it\'s a newer vehicle with limited recorded history.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Can I get a refund if I\'m not satisfied?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Due to the instant digital delivery nature of our reports, all sales are final. However, if you experience technical issues with your report delivery, please contact our support team for assistance.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Do you offer discounts for multiple reports?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Currently, each report is priced individually. However, we occasionally offer promotional pricing for multiple purchases. Contact us if you need multiple reports and we\'ll see how we can help.</p>
                </div>
            </div>
        </div>
        
        <div class="contact-support">
            <h2>Still have questions?</h2>
            <p>Our support team is here to help you make informed vehicle purchasing decisions.</p>
            <a href="mailto:support@vinreporting.com" class="btn-primary">
                <i class="fas fa-envelope"></i>
                Contact Support
            </a>
        </div>
    </div>
</section>

<script>
document.querySelectorAll(".faq-question").forEach(question => {
    question.addEventListener("click", function() {
        const item = this.parentElement;
        const answer = item.querySelector(".faq-answer");
        const icon = this.querySelector("i");
        
        item.classList.toggle("active");
        
        if (item.classList.contains("active")) {
            answer.style.maxHeight = answer.scrollHeight + "px";
            icon.classList.replace("fa-chevron-down", "fa-chevron-up");
        } else {
            answer.style.maxHeight = "0";
            icon.classList.replace("fa-chevron-up", "fa-chevron-down");
        }
    });
});
</script>

<?php include "includes/footer.php"; ?>';

createFile('faq.php', $faqContent);

// Create service pages
$servicePages = [
    "vehicle-history-reports" => [
        "title" => "Complete Vehicle History Reports",
        "description" => "Get comprehensive vehicle history information from trusted industry providers",
        "content" => "Our vehicle history reports provide you with essential information about any used vehicle before you make a purchase decision. We compile data from multiple trusted sources to give you the most complete picture of a vehicle\'s past."
    ],
    "accident-history-check" => [
        "title" => "Accident History Check",  
        "description" => "Discover any accident or damage history for complete peace of mind",
        "content" => "Our accident history checks reveal any reported accidents, collisions, or damage incidents. This critical information helps you avoid vehicles with hidden damage that could affect safety, performance, and resale value."
    ],
    "title-information" => [
        "title" => "Title Information & Verification",
        "description" => "Verify title status and identify potential title issues",
        "content" => "Title problems can be costly and complicated. Our title information service checks for liens, salvage titles, flood damage, lemon designations, and other title issues that could affect your ownership rights and the vehicle\'s value."
    ],
    "service-records" => [
        "title" => "Service & Maintenance Records",
        "description" => "Review detailed service history and maintenance records",
        "content" => "Regular maintenance is crucial for vehicle longevity. Our service records analysis shows you the vehicle\'s maintenance history, helping you understand how well the vehicle has been cared for and what future maintenance may be needed."
    ],
    "market-value-analysis" => [
        "title" => "Market Value Analysis",
        "description" => "Get accurate market pricing and value estimates",
        "content" => "Make informed pricing decisions with our market value analysis. We provide current market values, regional pricing trends, and depreciation information to help you negotiate the best deal or set the right selling price."
    ],
    "recall-information" => [
        "title" => "Recall Information & Safety Alerts",
        "description" => "Stay informed about safety recalls and manufacturer notices",
        "content" => "Safety recalls can affect vehicle performance and safety. Our recall information service identifies any open recalls, safety campaigns, or technical service bulletins that may apply to your vehicle, helping you stay safe and maintain your warranty."
    ]
];

foreach ($servicePages as $filename => $page) {
    $relatedLinks = '';
    foreach ($servicePages as $slug => $relatedPage) {
        if ($slug !== $filename) {
            $relatedLinks .= '                        <li><a href="' . $slug . '.php">' . $relatedPage["title"] . '</a></li>' . "\n";
        }
    }
    
    $serviceContent = '<?php
require_once "../includes/config.php";
$page_title = "' . $page["title"] . '";
include "../includes/header.php";
?>

<section class="service-page">
    <div class="container">
        <div class="service-header">
            <h1>\' . $page["title"] . \'</h1>
            <p class="lead">\' . $page["description"] . \'</p>
        </div>
        
        <div class="service-content">
            <div class="main-content">
                <h2>Why Choose Our \' . $page["title"] . \'?</h2>
                <p>\' . $page["content"] . \'</p>
                
                <div class="benefits">
                    <h3>Key Benefits:</h3>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Comprehensive data from trusted sources</li>
                        <li><i class="fas fa-check-circle"></i> Instant digital delivery</li>
                        <li><i class="fas fa-check-circle"></i> Easy-to-understand reports</li>
                        <li><i class="fas fa-check-circle"></i> Professional analysis and insights</li>
                    </ul>
                </div>
                
                <div class="cta-section">
                    <h3>Get Your Report Today</h3>
                    <p>Don\'t take risks when buying or selling a vehicle. Get the information you need to make confident decisions.</p>
                    <a href="../index.php" class="btn-primary">
                        <i class="fas fa-search"></i>
                        Get Vehicle Report
                    </a>
                </div>
            </div>
            
            <div class="sidebar">
                <div class="related-services">
                    <h3>Related Services</h3>
                    <ul>
\' . $relatedLinks . \'                    </ul>
                </div>
                
                <div class="contact-info">
                    <h3>Need Help?</h3>
                    <p>Our experts are here to help you understand your vehicle report.</p>
                    <a href="mailto:support@vinreporting.com" class="btn-secondary">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "../includes/footer.php"; ?>';
    
    createFile('services/' . $filename . '.php', $serviceContent);
}

// Add remaining admin files
$resendEmailContent = \'<?php
require_once "../includes/config.php";
require_once "../lib/email.php";

if (!isset($_SESSION["admin_logged_in"]) || !$_SESSION["admin_logged_in"]) {
    header("Location: login.php");
    exit;
}

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
    header("Location: index.php?error=order_not_found");
    exit;
}

// Resend email
if (sendOrderConfirmationEmail($order)) {
    header("Location: index.php?success=email_resent");
} else {
    header("Location: index.php?error=email_failed");
}
exit;
?>';

createFile('admin/resend-email.php', $resendEmailContent);

$exportOrdersContent = '<?php
require_once "../includes/config.php";

if (!isset($_SESSION["admin_logged_in"]) || !$_SESSION["admin_logged_in"]) {
    header("Location: login.php");
    exit;
}

$orders = get_orders();

// Set headers for CSV download
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=vinreporting_orders_" . date("Y-m-d") . ".csv");

$output = fopen("php://output", "w");

// Add CSV headers
fputcsv($output, [
    "Order ID",
    "Date",
    "Customer Name", 
    "Customer Email",
    "Customer Phone",
    "VIN",
    "Provider",
    "Plan",
    "Amount",
    "PayPal Order ID",
    "Status"
]);

// Add order data
foreach ($orders as $order) {
    fputcsv($output, [
        $order["order_id"],
        $order["created_at"],
        $order["customer_name"],
        $order["customer_email"],
        $order["customer_phone"] ?? "",
        $order["vin"],
        $order["provider"],
        $order["plan_name"],
        $order["amount"],
        $order["paypal_order_id"],
        $order["status"]
    ]);
}

fclose($output);
exit;
?>';

createFile('admin/export-orders.php', $exportOrdersContent);

logMessage("✅ Service pages and additional admin files created!");
logMessage("✅ All project files have been generated successfully!");

// Add CSS for the new pages
$additionalCss = \'
/* Additional styles for service pages, pricing, FAQ */

.pricing {
    padding: 3rem 0;
}

.pricing-header {
    text-align: center;
    margin-bottom: 3rem;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.pricing-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow);
    position: relative;
    transition: transform 0.3s ease;
}

.pricing-card:hover {
    transform: translateY(-5px);
}

.pricing-card.featured {
    border: 2px solid var(--primary-color);
    transform: scale(1.05);
}

.featured-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary-gradient);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.pricing-card h3 {
    text-align: center;
    margin-bottom: 1rem;
    color: var(--text-color);
}

.price {
    text-align: center;
    margin-bottom: 1rem;
}

.currency {
    font-size: 1.5rem;
    color: var(--text-light);
    vertical-align: top;
}

.amount {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-color);
}

.period {
    font-size: 1.5rem;
    color: var(--text-light);
}

.description {
    text-align: center;
    color: var(--text-light);
    margin-bottom: 2rem;
}

.features {
    list-style: none;
    margin-bottom: 2rem;
}

.features li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.features i {
    color: var(--success-color);
}

/* FAQ Styles */
.faq {
    padding: 3rem 0;
}

.faq-header {
    text-align: center;
    margin-bottom: 3rem;
}

.faq-content {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.faq-question {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

.faq-question:hover {
    background: var(--bg-light);
}

.faq-question h3 {
    margin: 0;
    color: var(--text-color);
}

.faq-question i {
    color: var(--primary-color);
    transition: transform 0.3s ease;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-answer p {
    padding: 0 1.5rem 1.5rem;
    margin: 0;
    color: var(--text-light);
    line-height: 1.6;
}

.contact-support {
    text-align: center;
    margin-top: 3rem;
    padding: 2rem;
    background: var(--bg-light);
    border-radius: 12px;
}

/* Service Page Styles */
.service-page {
    padding: 3rem 0;
}

.service-header {
    text-align: center;
    margin-bottom: 3rem;
}

.lead {
    font-size: 1.25rem;
    color: var(--text-light);
}

.service-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
}

.main-content {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow);
}

.benefits {
    margin: 2rem 0;
}

.benefits ul {
    list-style: none;
}

.benefits li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.benefits i {
    color: var(--success-color);
}

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.related-services,
.contact-info {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
}

.related-services ul {
    list-style: none;
}

.related-services li {
    margin-bottom: 0.5rem;
}

.related-services a {
    color: var(--text-light);
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-services a:hover {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .service-content {
        grid-template-columns: 1fr;
    }
    
    .pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .pricing-card.featured {
        transform: none;
    }
}\';

// Append additional CSS to the main CSS file
$existingCss = file_get_contents("assets/css/style.css");
file_put_contents("assets/css/style.css", $existingCss . "\n\n" . $additionalCss);

logMessage("✅ Additional CSS styles added!");

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