import React, { useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { Card } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { Badge } from '../components/ui/badge';
import { CheckCircle, CreditCard, Shield, Lock, ArrowLeft } from 'lucide-react';
import { toast } from 'sonner';
import axios from 'axios';

const CheckoutPage = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const [orderData, setOrderData] = useState(null);
  const [isProcessing, setIsProcessing] = useState(false);
  const [showReportPreview, setShowReportPreview] = useState(false);

  const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
  const API = `${BACKEND_URL}/api`;

  useEffect(() => {
    if (location.state?.orderData) {
      setOrderData(location.state.orderData);
    } else {
      // Redirect to home if no order data
      navigate('/');
    }
  }, [location, navigate]);

  const pricingPlans = {
    basic: { 
      price: 29.99, 
      name: 'Basic Report',
      features: ['Accident History', 'Title Information', 'Basic Records']
    },
    premium: { 
      price: 39.99, 
      name: 'Premium Report',
      features: ['Everything in Basic', 'Service Records', 'Previous Owners', 'Market Value']
    },
    ultimate: { 
      price: 49.99, 
      name: 'Ultimate Report',
      features: ['Everything in Premium', 'Detailed Photos', 'Recalls & Safety', 'Comprehensive Timeline']
    }
  };

  const handlePayment = async () => {
    if (!orderData) return;

    setIsProcessing(true);
    
    try {
      // Create order in backend
      const response = await axios.post(`${API}/orders`, {
        vin: orderData.vin,
        customer_name: orderData.customer_name,
        customer_email: orderData.customer_email,
        customer_phone: orderData.customer_phone,
        report_provider: orderData.report_provider,
        plan_type: orderData.plan_type,
        total_amount: pricingPlans[orderData.plan_type].price
      });

      // Show report preview
      setShowReportPreview(true);
      
      // Simulate payment processing
      setTimeout(() => {
        // Update payment status
        axios.put(`${API}/orders/${response.data.id}/payment`, {
          status: 'completed',
          payment_id: `PAY_${Date.now()}`
        });

        toast.success('Payment successful! Redirecting...');
        
        setTimeout(() => {
          navigate('/thank-you', { 
            state: { 
              orderData: response.data,
              paymentSuccess: true
            }
          });
        }, 1500);
      }, 3000);

    } catch (error) {
      console.error('Payment error:', error);
      toast.error('Payment failed. Please try again.');
      setIsProcessing(false);
    }
  };

  if (!orderData) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <p className="text-gray-400">Loading...</p>
        </div>
      </div>
    );
  }

  const selectedPlan = pricingPlans[orderData.plan_type];

  return (
    <div className="min-h-screen pt-20 pb-12">
      <div className="container mx-auto px-4">
        <div className="max-w-6xl mx-auto">
          {/* Header */}
          <div className="mb-8">
            <Button
              variant="ghost"
              onClick={() => navigate('/')}
              className="text-gray-400 hover:text-white mb-4"
            >
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back to Home
            </Button>
            <h1 className="text-3xl font-bold text-white mb-2">Secure Checkout</h1>
            <p className="text-gray-400">Complete your vehicle history report purchase</p>
          </div>

          <div className="grid lg:grid-cols-3 gap-8">
            {/* Order Summary */}
            <div className="lg:col-span-2">
              <Card className="glass-card mb-6">
                <h3 className="text-xl font-bold text-white mb-4">Order Details</h3>
                
                <div className="space-y-4">
                  <div className="grid grid-cols-2 gap-4 p-4 bg-white/5 rounded-lg">
                    <div>
                      <label className="text-sm text-gray-400">Vehicle VIN</label>
                      <p className="text-white font-mono text-lg">{orderData.vin}</p>
                    </div>
                    <div>
                      <label className="text-sm text-gray-400">Report Provider</label>
                      <p className="text-white capitalize">{orderData.report_provider}</p>
                    </div>
                  </div>

                  <div className="p-4 bg-gradient-to-r from-red-500/10 to-pink-500/10 rounded-lg border border-red-500/20">
                    <div className="flex justify-between items-start mb-3">
                      <div>
                        <h4 className="text-lg font-semibold text-white">{selectedPlan.name}</h4>
                        <Badge className="bg-red-500/20 text-red-400 border-red-500/30">
                          Most Popular
                        </Badge>
                      </div>
                      <div className="text-right">
                        <p className="text-2xl font-bold text-white">${selectedPlan.price}</p>
                        <p className="text-sm text-gray-400">One-time payment</p>
                      </div>
                    </div>
                    
                    <div className="space-y-2">
                      {selectedPlan.features.map((feature, index) => (
                        <div key={index} className="flex items-center space-x-2">
                          <CheckCircle className="w-4 h-4 text-green-400" />
                          <span className="text-sm text-gray-300">{feature}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </Card>

              {/* Customer Information */}
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-4">Customer Information</h3>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm text-gray-400">Full Name</label>
                    <p className="text-white">{orderData.customer_name}</p>
                  </div>
                  <div>
                    <label className="text-sm text-gray-400">Email</label>
                    <p className="text-white">{orderData.customer_email}</p>
                  </div>
                  <div>
                    <label className="text-sm text-gray-400">Phone</label>
                    <p className="text-white">{orderData.customer_phone}</p>
                  </div>
                  <div>
                    <label className="text-sm text-gray-400">Delivery Method</label>
                    <p className="text-white">Email (Instant)</p>
                  </div>
                </div>
              </Card>
            </div>

            {/* Payment Summary */}
            <div className="lg:col-span-1">
              <Card className="glass-card sticky top-24">
                <h3 className="text-xl font-bold text-white mb-6">Payment Summary</h3>
                
                <div className="space-y-4 mb-6">
                  <div className="flex justify-between">
                    <span className="text-gray-400">{selectedPlan.name}</span>
                    <span className="text-white">${selectedPlan.price}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-400">Processing Fee</span>
                    <span className="text-white">$0.00</span>
                  </div>
                  <div className="border-t border-white/10 pt-4">
                    <div className="flex justify-between text-lg font-semibold">
                      <span className="text-white">Total</span>
                      <span className="text-white">${selectedPlan.price}</span>
                    </div>
                  </div>
                </div>

                {/* Security Badges */}
                <div className="space-y-3 mb-6">
                  <div className="flex items-center space-x-2 text-sm text-gray-400">
                    <Shield className="w-4 h-4 text-green-400" />
                    <span>SSL Secured Payment</span>
                  </div>
                  <div className="flex items-center space-x-2 text-sm text-gray-400">
                    <Lock className="w-4 h-4 text-green-400" />
                    <span>256-bit Encryption</span>
                  </div>
                  <div className="flex items-center space-x-2 text-sm text-gray-400">
                    <CheckCircle className="w-4 h-4 text-green-400" />
                    <span>30-day Money Back Guarantee</span>
                  </div>
                </div>

                {/* Payment Button */}
                <Button
                  onClick={handlePayment}
                  disabled={isProcessing}
                  className="w-full py-4 text-lg font-semibold bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white border-0"
                  data-testid="complete-payment-btn"
                >
                  {isProcessing ? (
                    <div className="flex items-center space-x-2">
                      <div className="spinner"></div>
                      <span>Processing Payment...</span>
                    </div>
                  ) : (
                    <div className="flex items-center space-x-2">
                      <CreditCard className="w-5 h-5" />
                      <span>Complete Payment - ${selectedPlan.price}</span>
                    </div>
                  )}
                </Button>

                <p className="text-xs text-gray-400 text-center mt-3">
                  By completing this purchase, you agree to our Terms of Service and Privacy Policy.
                </p>
              </Card>
            </div>
          </div>
        </div>
      </div>

      {/* Report Preview Modal */}
      {showReportPreview && (
        <div className="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <Card className="glass-card max-w-2xl w-full">
            <div className="text-center space-y-6">
              <div className="w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mx-auto flex items-center justify-center">
                <CheckCircle className="w-10 h-10 text-white" />
              </div>
              
              <div>
                <h3 className="text-2xl font-bold text-white mb-2">Payment Successful!</h3>
                <p className="text-gray-400">Your vehicle history report is being generated...</p>
              </div>

              {/* Blurred Report Preview */}
              <div className="bg-white/5 rounded-lg p-6 filter blur-sm">
                <div className="space-y-4">
                  <div className="flex justify-between items-center border-b border-white/10 pb-2">
                    <span className="text-white font-semibold">Vehicle History Report</span>
                    <Badge className="bg-green-500/20 text-green-400">Clean Title</Badge>
                  </div>
                  
                  <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span className="text-gray-400">VIN:</span>
                      <span className="text-white ml-2">{orderData.vin}</span>
                    </div>
                    <div>
                      <span className="text-gray-400">Year:</span>
                      <span className="text-white ml-2">2018</span>
                    </div>
                    <div>
                      <span className="text-gray-400">Make/Model:</span>
                      <span className="text-white ml-2">Honda Accord</span>
                    </div>
                    <div>
                      <span className="text-gray-400">Accidents:</span>
                      <span className="text-green-400 ml-2">No accidents reported</span>
                    </div>
                  </div>
                  
                  <div className="space-y-2">
                    <p className="text-gray-400 text-sm">Service History:</p>
                    <div className="space-y-1">
                      <div className="text-xs text-gray-500">• Regular maintenance at 15,000 miles</div>
                      <div className="text-xs text-gray-500">• Oil change at 22,000 miles</div>
                      <div className="text-xs text-gray-500">• Brake inspection at 28,000 miles</div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="bg-gradient-to-r from-red-500/10 to-pink-500/10 rounded-lg p-4 border border-red-500/20">
                <p className="text-white font-semibold mb-2">Report Preview (10%)</p>
                <p className="text-gray-400 text-sm">
                  Full detailed report will be sent to your email within seconds.
                </p>
              </div>

              <p className="text-sm text-gray-400">
                Please wait while we complete your order...
              </p>
            </div>
          </Card>
        </div>
      )}
    </div>
  );
};

export default CheckoutPage;