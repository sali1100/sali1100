import React from 'react';
import { useNavigate } from 'react-router-dom';
import { Card } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { Badge } from '../components/ui/badge';
import { CheckCircle, Star, Zap, Crown } from 'lucide-react';

const PricingPage = () => {
  const navigate = useNavigate();

  const plans = [
    {
      id: 'basic',
      name: 'Basic Plan',
      price: 34.99,
      reports: 1,
      icon: <Star className="w-8 h-8" />,
      popular: false,
      features: [
        'Complete Vehicle History',
        'Accident Records',
        'Title Information',
        'Basic Service Records',
        'Email Delivery',
        '24/7 Support'
      ],
      gradient: 'from-gray-500 to-gray-700',
      borderColor: 'border-gray-500/30'
    },
    {
      id: 'premium',
      name: 'Premium Plan',
      price: 44.99,
      reports: 3,
      icon: <Zap className="w-8 h-8" />,
      popular: true,
      features: [
        'Everything in Basic',
        '3 Vehicle Reports',
        'Detailed Service History',
        'Previous Owner Details',
        'Market Value Analysis',
        'Priority Support',
        'Mobile Access'
      ],
      gradient: 'from-blue-500 to-cyan-500',
      borderColor: 'border-blue-500/30'
    },
    {
      id: 'ultimate',
      name: 'Ultimate Plan',
      price: 64.99,
      reports: 5,
      icon: <Crown className="w-8 h-8" />,
      popular: false,
      features: [
        'Everything in Premium',
        '5 Vehicle Reports',
        'Comprehensive Photos',
        'Recall Information',
        'Fleet Analysis',
        'Advanced Analytics',
        'White-Glove Service',
        'Custom Reports'
      ],
      gradient: 'from-purple-500 to-pink-500',
      borderColor: 'border-purple-500/30'
    }
  ];

  const handleSelectPlan = (plan) => {
    // Navigate to home with preselected plan
    navigate('/', { 
      state: { 
        preselectedPlan: plan.id,
        planPrice: plan.price 
      }
    });
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
        <div className="max-w-6xl mx-auto">
          {/* Header */}
          <div className="text-center mb-16">
            <Badge className="bg-gradient-to-r from-blue-500/20 to-cyan-500/20 text-blue-400 border-blue-500/30 mb-6">
              🚗 Transparent Pricing
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Choose Your Plan
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Get comprehensive vehicle history reports at competitive prices. 
              Choose the plan that fits your needs perfectly.
            </p>
          </div>

          {/* Pricing Cards */}
          <div className="grid md:grid-cols-3 gap-8 mb-16">
            {plans.map((plan, index) => (
              <Card 
                key={plan.id} 
                className={`glass-card relative overflow-hidden ${
                  plan.popular ? 'scale-105 border-blue-500/50' : ''
                }`}
              >
                {plan.popular && (
                  <div className="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <Badge className="bg-gradient-to-r from-blue-500 to-cyan-500 text-white border-0 px-6 py-2">
                      Most Popular
                    </Badge>
                  </div>
                )}

                <div className="text-center space-y-6">
                  {/* Icon */}
                  <div className={`w-16 h-16 bg-gradient-to-r ${plan.gradient} rounded-full mx-auto flex items-center justify-center text-white`}>
                    {plan.icon}
                  </div>

                  {/* Plan Details */}
                  <div>
                    <h3 className="text-2xl font-bold text-white mb-2">{plan.name}</h3>
                    <div className="mb-4">
                      <span className="text-4xl font-bold text-white">${plan.price}</span>
                      <span className="text-gray-400 ml-2">/ {plan.reports} report{plan.reports > 1 ? 's' : ''}</span>
                    </div>
                    <p className="text-gray-400">
                      Perfect for {plan.reports === 1 ? 'single vehicle checks' : `checking up to ${plan.reports} vehicles`}
                    </p>
                  </div>

                  {/* Features */}
                  <div className="space-y-3 text-left">
                    {plan.features.map((feature, featureIndex) => (
                      <div key={featureIndex} className="flex items-center space-x-3">
                        <CheckCircle className="w-5 h-5 text-green-400 flex-shrink-0" />
                        <span className="text-gray-300 text-sm">{feature}</span>
                      </div>
                    ))}
                  </div>

                  {/* CTA Button */}
                  <Button
                    onClick={() => handleSelectPlan(plan)}
                    className={`w-full py-4 text-lg font-semibold bg-gradient-to-r ${plan.gradient} hover:scale-105 text-white border-0 transition-all duration-300`}
                    data-testid={`select-${plan.id}-plan`}
                  >
                    Choose {plan.name}
                  </Button>
                </div>

                {/* Background Decoration */}
                <div className={`absolute inset-0 bg-gradient-to-br ${plan.gradient} opacity-5 pointer-events-none`}></div>
              </Card>
            ))}
          </div>

          {/* Features Comparison */}
          <Card className="glass-card">
            <div className="text-center mb-8">
              <h3 className="text-2xl font-bold text-white mb-4">What's Included</h3>
              <p className="text-gray-400">Compare features across all plans</p>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-white/10">
                    <th className="text-left py-4 text-white font-semibold">Features</th>
                    <th className="text-center py-4 text-white font-semibold">Basic</th>
                    <th className="text-center py-4 text-white font-semibold">Premium</th>
                    <th className="text-center py-4 text-white font-semibold">Ultimate</th>
                  </tr>
                </thead>
                <tbody className="space-y-2">
                  {[
                    ['Number of Reports', '1', '3', '5'],
                    ['Vehicle History', '✓', '✓', '✓'],
                    ['Accident Records', '✓', '✓', '✓'],
                    ['Service History', 'Basic', 'Detailed', 'Comprehensive'],
                    ['Market Value Analysis', '✗', '✓', '✓'],
                    ['Photo Gallery', '✗', '✗', '✓'],
                    ['Recall Information', '✗', '✗', '✓'],
                    ['Priority Support', '✗', '✓', '✓'],
                    ['Custom Reports', '✗', '✗', '✓']
                  ].map(([feature, basic, premium, ultimate], index) => (
                    <tr key={index} className="border-b border-white/5">
                      <td className="py-3 text-gray-300">{feature}</td>
                      <td className="py-3 text-center text-gray-400">{basic}</td>
                      <td className="py-3 text-center text-gray-400">{premium}</td>
                      <td className="py-3 text-center text-gray-400">{ultimate}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </Card>

          {/* FAQ Section */}
          <div className="text-center mt-16">
            <h3 className="text-2xl font-bold text-white mb-8">Frequently Asked Questions</h3>
            <div className="grid md:grid-cols-2 gap-6">
              {[
                {
                  question: "How quickly will I receive my report?",
                  answer: "All reports are delivered instantly via email upon successful payment completion."
                },
                {
                  question: "Can I use multiple reports from my plan?",
                  answer: "Yes, Premium and Ultimate plans allow you to run multiple vehicle checks with different VINs."
                },
                {
                  question: "What payment methods do you accept?",
                  answer: "We accept all major credit cards, debit cards, and PayPal for secure transactions."
                },
                {
                  question: "Is there a money-back guarantee?",
                  answer: "Yes, we offer a 30-day satisfaction guarantee. If you're not satisfied, we'll refund your purchase."
                }
              ].map((faq, index) => (
                <Card key={index} className="glass-card text-left">
                  <h4 className="font-semibold text-white mb-2">{faq.question}</h4>
                  <p className="text-gray-400 text-sm">{faq.answer}</p>
                </Card>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PricingPage;