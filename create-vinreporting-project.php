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

// Checkout page
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

logMessage("✅ Core files created successfully!");

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