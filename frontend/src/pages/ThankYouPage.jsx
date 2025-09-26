import React, { useEffect } from 'react';
import { useLocation, useNavigate, Link } from 'react-router-dom';
import { Card } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { Badge } from '../components/ui/badge';
import { CheckCircle, Mail, Download, ArrowRight, Home, FileText } from 'lucide-react';

const ThankYouPage = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const orderData = location.state?.orderData;

  useEffect(() => {
    if (!orderData) {
      navigate('/');
    }
  }, [orderData, navigate]);

  if (!orderData) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <p className="text-gray-400">Redirecting...</p>
        </div>
      </div>
    );
  }

  const pricingPlans = {
    basic: { price: 29.99, name: 'Basic Report' },
    premium: { price: 39.99, name: 'Premium Report' },
    ultimate: { price: 49.99, name: 'Ultimate Report' }
  };

  return (
    <div className="min-h-screen pt-20 pb-12">
      {/* Floating Particles Background */}
      <div className="particles-bg">
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
      </div>

      <div className="container mx-auto px-4">
        <div className="max-w-4xl mx-auto">
          
          {/* Success Animation */}
          <div className="text-center mb-12">
            <div className="w-24 h-24 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mx-auto flex items-center justify-center mb-6 animate-pulse">
              <CheckCircle className="w-12 h-12 text-white" />
            </div>
            
            <h1 className="text-4xl lg:text-5xl font-bold text-white mb-4">
              Thank You for Your Order!
            </h1>
            <p className="text-xl text-gray-300 max-w-2xl mx-auto">
              Your vehicle history report has been successfully generated and will be delivered to your email shortly.
            </p>
          </div>

          {/* Order Summary */}
          <Card className="glass-card mb-8">
            <div className="flex items-center space-x-4 mb-6">
              <div className="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-lg flex items-center justify-center">
                <FileText className="w-6 h-6 text-white" />
              </div>
              <div>
                <h3 className="text-2xl font-bold text-white">Order Summary</h3>
                <p className="text-gray-400">Order ID: {orderData.id?.slice(0, 8).toUpperCase()}</p>
              </div>
            </div>

            <div className="grid md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <label className="text-sm font-medium text-gray-400">Vehicle Information</label>
                  <div className="bg-white/5 rounded-lg p-4 mt-2">
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <span className="text-xs text-gray-400">VIN Number</span>
                        <p className="text-white font-mono">{orderData.vin}</p>
                      </div>
                      <div>
                        <span className="text-xs text-gray-400">Report Provider</span>
                        <p className="text-white capitalize">{orderData.report_provider}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label className="text-sm font-medium text-gray-400">Customer Details</label>
                  <div className="bg-white/5 rounded-lg p-4 mt-2">
                    <div className="space-y-2">
                      <div>
                        <span className="text-xs text-gray-400">Name</span>
                        <p className="text-white">{orderData.customer_name}</p>
                      </div>
                      <div>
                        <span className="text-xs text-gray-400">Email</span>
                        <p className="text-white">{orderData.customer_email}</p>
                      </div>
                      <div>
                        <span className="text-xs text-gray-400">Phone</span>
                        <p className="text-white">{orderData.customer_phone}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="text-sm font-medium text-gray-400">Report Package</label>
                  <div className="bg-gradient-to-r from-red-500/10 to-pink-500/10 rounded-lg p-4 mt-2 border border-red-500/20">
                    <div className="flex justify-between items-start mb-3">
                      <div>
                        <h4 className="text-lg font-semibold text-white">
                          {pricingPlans[orderData.plan_type]?.name}
                        </h4>
                        <Badge className="bg-red-500/20 text-red-400 border-red-500/30">
                          Premium Package
                        </Badge>
                      </div>
                      <div className="text-right">
                        <p className="text-2xl font-bold text-white">
                          ${pricingPlans[orderData.plan_type]?.price}
                        </p>
                        <p className="text-sm text-gray-400">Paid</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label className="text-sm font-medium text-gray-400">Payment Status</label>
                  <div className="bg-green-500/10 rounded-lg p-4 mt-2 border border-green-500/20">
                    <div className="flex items-center space-x-2">
                      <CheckCircle className="w-5 h-5 text-green-400" />
                      <span className="text-green-400 font-semibold">Payment Successful</span>
                    </div>
                    <p className="text-sm text-gray-400 mt-1">
                      Transaction completed on {new Date().toLocaleDateString()}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </Card>

          {/* What's Next */}
          <Card className="glass-card mb-8">
            <h3 className="text-xl font-bold text-white mb-6">What Happens Next?</h3>
            
            <div className="grid md:grid-cols-3 gap-6">
              <div className="text-center space-y-3">
                <div className="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center">
                  <Mail className="w-8 h-8 text-white" />
                </div>
                <div>
                  <h4 className="font-semibold text-white mb-1">Email Delivery</h4>
                  <p className="text-sm text-gray-400">
                    Your detailed report will be sent to {orderData.customer_email} within the next few minutes.
                  </p>
                </div>
              </div>

              <div className="text-center space-y-3">
                <div className="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mx-auto flex items-center justify-center">
                  <Download className="w-8 h-8 text-white" />
                </div>
                <div>
                  <h4 className="font-semibold text-white mb-1">Download Report</h4>
                  <p className="text-sm text-gray-400">
                    Click the download link in your email to access your comprehensive vehicle history report.
                  </p>
                </div>
              </div>

              <div className="text-center space-y-3">
                <div className="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mx-auto flex items-center justify-center">
                  <FileText className="w-8 h-8 text-white" />
                </div>
                <div>
                  <h4 className="font-semibold text-white mb-1">Review Details</h4>
                  <p className="text-sm text-gray-400">
                    Review all vehicle history details, including accidents, title information, and service records.
                  </p>
                </div>
              </div>
            </div>
          </Card>

          {/* Action Buttons */}
          <div className="text-center space-y-4">
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button 
                asChild
                className="bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white border-0 px-8 py-3"
              >
                <Link to="/sample-report">
                  View Sample Report
                  <ArrowRight className="ml-2 w-4 h-4" />
                </Link>
              </Button>
              
              <Button 
                asChild
                variant="outline"
                className="border-white/20 text-white hover:bg-white/10 px-8 py-3"
              >
                <Link to="/">
                  <Home className="mr-2 w-4 h-4" />
                  Back to Home
                </Link>
              </Button>
            </div>

            <p className="text-sm text-gray-400 max-w-md mx-auto">
              Need help? Contact our support team at{' '}
              <a href="mailto:support@vinreporting.com" className="text-red-400 hover:text-red-300">
                support@vinreporting.com
              </a>
              {' '}or call{' '}
              <a href="tel:1-800-VIN-REPORT" className="text-red-400 hover:text-red-300">
                1-800-VIN-REPORT
              </a>
            </p>
          </div>

          {/* Trust Message */}
          <div className="bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-2xl p-6 mt-8 border border-green-500/20">
            <div className="text-center">
              <CheckCircle className="w-8 h-8 text-green-400 mx-auto mb-3" />
              <h4 className="text-lg font-semibold text-white mb-2">
                Thank you for choosing VinReporting.com
              </h4>
              <p className="text-gray-400">
                We're committed to providing you with the most accurate and comprehensive vehicle history information. 
                Your trust in us helps millions of car buyers make informed decisions every day.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ThankYouPage;