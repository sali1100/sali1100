import React, { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { Button } from '../components/ui/button';
import { Card } from '../components/ui/card';
import { Badge } from '../components/ui/badge';
import { CheckCircle, Shield, Clock, Star, ArrowRight, Car, Search, FileText } from 'lucide-react';

const HomePage = () => {
  const navigate = useNavigate();
  const location = useLocation();
  
  // Check for preselected plan from pricing page
  const preselectedPlan = location.state?.preselectedPlan || 'premium';
  
  const [formData, setFormData] = useState({
    vin: '',
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    report_provider: 'carfax',
    plan_type: preselectedPlan
  });

  const [isFormValid, setIsFormValid] = useState(false);
  const [showPreview, setShowPreview] = useState(false);

  const validateVIN = (vin) => {
    return vin.length === 17 && /^[A-HJ-NPR-Z0-9]{17}$/i.test(vin);
  };

  const validateEmail = (email) => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  };

  const validatePhone = (phone) => {
    return /^\d{10,}$/.test(phone.replace(/\D/g, ''));
  };

  useEffect(() => {
    const isValid = 
      validateVIN(formData.vin) &&
      formData.customer_name.trim().length > 0 &&
      validateEmail(formData.customer_email) &&
      validatePhone(formData.customer_phone) &&
      formData.report_provider &&
      formData.plan_type;
    
    setIsFormValid(isValid);
  }, [formData]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value.toUpperCase()
    }));
  };

  const handleGetReport = () => {
    if (isFormValid) {
      setShowPreview(true);
      setTimeout(() => {
        navigate('/checkout', { state: { orderData: formData } });
      }, 2000);
    }
  };

  const pricingPlans = {
    basic: { price: 29.99, name: 'Basic Report' },
    premium: { price: 39.99, name: 'Premium Report' },
    ultimate: { price: 49.99, name: 'Ultimate Report' }
  };

  return (
    <div className="min-h-screen">
      {/* Floating Particles Background */}
      <div className="particles-bg">
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
      </div>

      {/* Hero Section */}
      <section className="hero-section relative">
        <div className="hero-bg"></div>
        <div className="container mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center">
          {/* Left Column - Content */}
          <div className="space-y-8 fade-in-up">
            <div className="space-y-4">
              <Badge className="bg-gradient-to-r from-red-500/20 to-pink-500/20 text-red-400 border-red-500/30">
                🚗 Premium Vehicle History Reports
              </Badge>
              <h1 className="text-6xl lg:text-7xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent">
                Know Your Car's
                <span className="block bg-gradient-to-r from-red-500 to-pink-500 bg-clip-text text-transparent">
                  True Story
                </span>
              </h1>
              <p className="text-xl text-gray-300 leading-relaxed">
                Get comprehensive vehicle history reports powered by Carfax and AutoCheck. 
                Uncover hidden problems, verify mileage, and buy with confidence.
              </p>
            </div>

            {/* Trust Badges */}
            <div className="flex flex-wrap gap-4">
              <div className="flex items-center space-x-2 glass px-4 py-2 rounded-full">
                <Shield className="w-5 h-5 text-green-400" />
                <span className="text-sm text-gray-300">Trusted by 2M+ Buyers</span>
              </div>
              <div className="flex items-center space-x-2 glass px-4 py-2 rounded-full">
                <Clock className="w-5 h-5 text-blue-400" />
                <span className="text-sm text-gray-300">Instant Delivery</span>
              </div>
              <div className="flex items-center space-x-2 glass px-4 py-2 rounded-full">
                <Star className="w-5 h-5 text-yellow-400" />
                <span className="text-sm text-gray-300">4.9/5 Rating</span>
              </div>
            </div>
          </div>

          {/* Right Column - Order Form */}
          <div className="glass-card relative">
            <div className="absolute inset-0 bg-gradient-to-r from-red-500/10 to-pink-500/10 rounded-2xl blur-xl"></div>
            <div className="relative space-y-6">
              <div className="text-center">
                <h3 className="text-2xl font-bold text-white mb-2">Get Your Report Now</h3>
                <p className="text-gray-400">Enter your vehicle details below</p>
              </div>

              <form className="space-y-4" onSubmit={(e) => e.preventDefault()}>
                {/* VIN Input */}
                <div>
                  <label className="block text-sm font-medium text-gray-300 mb-2">
                    Vehicle Identification Number (VIN)
                  </label>
                  <input
                    type="text"
                    name="vin"
                    value={formData.vin}
                    onChange={handleInputChange}
                    placeholder="Enter 17-digit VIN"
                    className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                    maxLength="17"
                  />
                  {formData.vin && !validateVIN(formData.vin) && (
                    <p className="text-red-400 text-sm mt-1">Please enter a valid 17-character VIN</p>
                  )}
                </div>

                {/* Provider Selection */}
                <div>
                  <label className="block text-sm font-medium text-gray-300 mb-2">Report Provider</label>
                  <div className="grid grid-cols-2 gap-2">
                    <button
                      type="button"
                      onClick={() => setFormData(prev => ({...prev, report_provider: 'carfax'}))}
                      className={`p-3 rounded-lg border transition-all ${
                        formData.report_provider === 'carfax'
                          ? 'bg-red-500/20 border-red-500 text-red-400'
                          : 'bg-white/5 border-white/10 text-gray-400 hover:border-red-500/50'
                      }`}
                    >
                      Carfax
                    </button>
                    <button
                      type="button"
                      onClick={() => setFormData(prev => ({...prev, report_provider: 'autocheck'}))}
                      className={`p-3 rounded-lg border transition-all ${
                        formData.report_provider === 'autocheck'
                          ? 'bg-red-500/20 border-red-500 text-red-400'
                          : 'bg-white/5 border-white/10 text-gray-400 hover:border-red-500/50'
                      }`}
                    >
                      AutoCheck
                    </button>
                  </div>
                </div>

                {/* Plan Selection */}
                <div>
                  <label className="block text-sm font-medium text-gray-300 mb-2">Select Plan</label>
                  <div className="space-y-2">
                    {Object.entries(pricingPlans).map(([key, plan]) => (
                      <button
                        key={key}
                        type="button"
                        onClick={() => setFormData(prev => ({...prev, plan_type: key}))}
                        className={`w-full p-3 rounded-lg border transition-all flex justify-between items-center ${
                          formData.plan_type === key
                            ? 'bg-red-500/20 border-red-500 text-red-400'
                            : 'bg-white/5 border-white/10 text-gray-400 hover:border-red-500/50'
                        }`}
                      >
                        <span>{plan.name}</span>
                        <span className="font-bold">${plan.price}</span>
                      </button>
                    ))}
                  </div>
                </div>

                {/* Customer Details */}
                <div className="grid grid-cols-1 gap-4">
                  <input
                    type="text"
                    name="customer_name"
                    value={formData.customer_name}
                    onChange={(e) => setFormData(prev => ({...prev, customer_name: e.target.value}))}
                    placeholder="Full Name"
                    className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                  />
                  <input
                    type="email"
                    name="customer_email"
                    value={formData.customer_email}
                    onChange={(e) => setFormData(prev => ({...prev, customer_email: e.target.value}))}
                    placeholder="Email Address"
                    className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                  />
                  <input
                    type="tel"
                    name="customer_phone"
                    value={formData.customer_phone}
                    onChange={(e) => setFormData(prev => ({...prev, customer_phone: e.target.value}))}
                    placeholder="Phone Number"
                    className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                  />
                </div>

                {/* Get Report Button */}
                <Button
                  onClick={handleGetReport}
                  disabled={!isFormValid}
                  className={`w-full py-4 text-lg font-semibold rounded-lg transition-all ${
                    isFormValid
                      ? 'bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white shadow-lg hover:shadow-red-500/25'
                      : 'bg-gray-600 text-gray-400 cursor-not-allowed'
                  }`}
                  data-testid="get-report-btn"
                >
                  {showPreview ? 'Processing...' : `Get Report - $${pricingPlans[formData.plan_type]?.price}`}
                  <ArrowRight className="ml-2 w-5 h-5" />
                </Button>
              </form>
            </div>
          </div>
        </div>
      </section>

      {/* How It Works Section */}
      <section className="section-padding bg-gradient-to-b from-gray-900/50 to-black/50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-white mb-4">How It Works</h2>
            <p className="text-gray-400 text-lg max-w-2xl mx-auto">
              Get your comprehensive vehicle history report in three simple steps
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {[
              {
                icon: <Search className="w-12 h-12" />,
                title: 'Enter VIN',
                description: 'Input your 17-digit Vehicle Identification Number and select your preferred report provider'
              },
              {
                icon: <FileText className="w-12 h-12" />,
                title: 'Choose Plan',
                description: 'Select from our Basic, Premium, or Ultimate report packages based on your needs'
              },
              {
                icon: <CheckCircle className="w-12 h-12" />,
                title: 'Get Report',
                description: 'Receive your detailed vehicle history report instantly via email'
              }
            ].map((step, index) => (
              <Card key={index} className="glass-card text-center">
                <div className="text-red-500 mb-4 flex justify-center">
                  {step.icon}
                </div>
                <h3 className="text-xl font-bold text-white mb-2">{step.title}</h3>
                <p className="text-gray-400">{step.description}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="section-padding">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-white mb-4">Why Choose VinReporting?</h2>
            <p className="text-gray-400 text-lg max-w-2xl mx-auto">
              Trusted by millions of car buyers and sellers across North America
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[
              {
                icon: <Shield className="w-8 h-8" />,
                title: 'Comprehensive Reports',
                description: 'Access to both Carfax and AutoCheck databases for complete vehicle history'
              },
              {
                icon: <Clock className="w-8 h-8" />,
                title: 'Instant Delivery',
                description: 'Get your report delivered to your email within seconds of purchase'
              },
              {
                icon: <Star className="w-8 h-8" />,
                title: '99.9% Accuracy',
                description: 'Verified data from trusted sources including DMV, insurance companies, and service centers'
              },
              {
                icon: <Car className="w-8 h-8" />,
                title: 'All Vehicle Types',
                description: 'Support for cars, trucks, motorcycles, RVs, and commercial vehicles'
              },
              {
                icon: <CheckCircle className="w-8 h-8" />,
                title: 'Money-Back Guarantee',
                description: '30-day satisfaction guarantee - get your money back if not completely satisfied'
              },
              {
                icon: <FileText className="w-8 h-8" />,
                title: 'Detailed Analysis',
                description: 'Accident history, title information, service records, and ownership details'
              }
            ].map((feature, index) => (
              <Card key={index} className="glass-card">
                <div className="text-red-500 mb-4">
                  {feature.icon}
                </div>
                <h3 className="text-lg font-bold text-white mb-2">{feature.title}</h3>
                <p className="text-gray-400 text-sm">{feature.description}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Preview Modal */}
      {showPreview && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <Card className="glass-card max-w-md w-full text-center">
            <div className="space-y-4">
              <div className="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-500 rounded-full mx-auto flex items-center justify-center">
                <CheckCircle className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-2xl font-bold text-white">Processing Your Request</h3>
              <p className="text-gray-400">
                Generating your vehicle history report preview...
              </p>
              <div className="bg-white/10 rounded-lg p-4 text-left space-y-2">
                <div className="flex justify-between">
                  <span className="text-gray-400">VIN:</span>
                  <span className="text-white font-mono">
                    {formData.vin.substring(0, 3)}***************{formData.vin.slice(-1)}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-400">Report Type:</span>
                  <span className="text-white">{pricingPlans[formData.plan_type]?.name}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-400">Provider:</span>
                  <span className="text-white capitalize">{formData.report_provider}</span>
                </div>
              </div>
              <p className="text-sm text-gray-400">
                Redirecting to secure checkout...
              </p>
            </div>
          </Card>
        </div>
      )}
    </div>
  );
};

export default HomePage;