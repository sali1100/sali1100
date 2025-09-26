import React, { useState } from 'react';
import { Card } from '../components/ui/card';
import { Badge } from '../components/ui/badge';
import { ChevronDown, ChevronUp, HelpCircle, MessageCircle, Phone, Mail } from 'lucide-react';

const FAQPage = () => {
  const [openItems, setOpenItems] = useState(new Set([0])); // First item open by default

  const toggleItem = (index) => {
    const newOpenItems = new Set(openItems);
    if (newOpenItems.has(index)) {
      newOpenItems.delete(index);
    } else {
      newOpenItems.add(index);
    }
    setOpenItems(newOpenItems);
  };

  const faqs = [
    {
      category: "General",
      questions: [
        {
          question: "What is a vehicle history report?",
          answer: "A vehicle history report is a comprehensive document that provides detailed information about a vehicle's past, including accident history, title information, service records, previous owners, and more. It helps you make informed decisions when buying or selling a vehicle."
        },
        {
          question: "How quickly will I receive my report?",
          answer: "All reports are delivered instantly via email upon successful payment completion. You'll receive your comprehensive vehicle history report within seconds of your purchase."
        },
        {
          question: "What information do I need to order a report?",
          answer: "You only need the 17-digit Vehicle Identification Number (VIN) to order a report. The VIN is typically found on the dashboard near the windshield, driver's side door jamb, or vehicle registration documents."
        }
      ]
    },
    {
      category: "Pricing & Plans",
      questions: [
        {
          question: "What's the difference between your pricing plans?",
          answer: "Basic Plan ($34.99) includes 1 report with essential vehicle history. Premium Plan ($44.99) offers 3 reports with detailed service history and market analysis. Ultimate Plan ($64.99) provides 5 reports with comprehensive data including photos and recall information."
        },
        {
          question: "Can I use multiple reports from my plan?",
          answer: "Yes! Premium and Ultimate plans allow you to run multiple vehicle checks with different VINs. Each report counts toward your plan limit, and unused reports don't expire for 12 months."
        },
        {
          question: "Do you offer refunds?",
          answer: "Yes, we offer a 30-day money-back satisfaction guarantee. If you're not completely satisfied with your report, contact our support team for a full refund."
        }
      ]
    },
    {
      category: "Report Content",
      questions: [
        {
          question: "What's included in your vehicle history reports?",
          answer: "Our reports include accident history, title information, service records, previous ownership details, mileage verification, recall information, market value analysis, and more. The exact content varies by plan level."
        },
        {
          question: "How accurate is the information in your reports?",
          answer: "We source data from trusted providers including DMV records, insurance companies, service centers, and auction houses. Our reports achieve 99.9% accuracy, though we recommend using reports as one factor in your vehicle evaluation."
        },
        {
          question: "Do you cover all vehicle types?",
          answer: "Yes, we provide reports for cars, trucks, motorcycles, RVs, and commercial vehicles manufactured from 1981 onwards. Coverage may vary for very rare or specialty vehicles."
        }
      ]
    },
    {
      category: "Technical Support",
      questions: [
        {
          question: "What payment methods do you accept?",
          answer: "We accept all major credit cards (Visa, MasterCard, American Express, Discover), debit cards, and PayPal. All transactions are secured with 256-bit SSL encryption."
        },
        {
          question: "I can't find my VIN. Where should I look?",
          answer: "The VIN is typically located on the dashboard (visible through windshield), driver's side door jamb, vehicle registration, insurance card, or title document. It's always 17 characters long with no spaces."
        },
        {
          question: "Can I get a report for vehicles outside the US?",
          answer: "Currently, our reports cover vehicles with US and Canadian history. We're working to expand coverage to other regions. Contact support for specific international vehicle inquiries."
        }
      ]
    }
  ];

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
          {/* Header */}
          <div className="text-center mb-16">
            <Badge className="bg-gradient-to-r from-blue-500/20 to-cyan-500/20 text-blue-400 border-blue-500/30 mb-6">
              ❓ Frequently Asked Questions
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              How Can We Help?
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Find answers to common questions about our vehicle history reports, 
              pricing, and services. Can't find what you're looking for? Contact our support team.
            </p>
          </div>

          {/* FAQ Sections */}
          <div className="space-y-8">
            {faqs.map((category, categoryIndex) => (
              <Card key={categoryIndex} className="glass-card">
                <h3 className="text-2xl font-bold text-white mb-6 flex items-center">
                  <HelpCircle className="w-6 h-6 text-blue-400 mr-3" />
                  {category.category}
                </h3>
                
                <div className="space-y-4">
                  {category.questions.map((faq, questionIndex) => {
                    const globalIndex = categoryIndex * 10 + questionIndex; // Unique index
                    const isOpen = openItems.has(globalIndex);
                    
                    return (
                      <div
                        key={questionIndex}
                        className="border border-white/10 rounded-lg overflow-hidden transition-all duration-300 hover:border-blue-500/30"
                      >
                        <button
                          onClick={() => toggleItem(globalIndex)}
                          className="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-white/5 transition-colors"
                        >
                          <span className="font-semibold text-white pr-4">
                            {faq.question}
                          </span>
                          <div className="text-blue-400 flex-shrink-0">
                            {isOpen ? (
                              <ChevronUp className="w-5 h-5" />
                            ) : (
                              <ChevronDown className="w-5 h-5" />
                            )}
                          </div>
                        </button>
                        
                        <div
                          className={`overflow-hidden transition-all duration-300 ${
                            isOpen ? 'max-h-96 opacity-100' : 'max-h-0 opacity-0'
                          }`}
                        >
                          <div className="px-6 pb-4 text-gray-300 leading-relaxed">
                            {faq.answer}
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </Card>
            ))}
          </div>

          {/* Contact Support */}
          <Card className="glass-card mt-12 text-center">
            <h3 className="text-2xl font-bold text-white mb-4">Still Need Help?</h3>
            <p className="text-gray-400 mb-8">
              Our support team is here to help you with any questions or concerns.
            </p>
            
            <div className="grid md:grid-cols-3 gap-6">
              <div className="space-y-3">
                <div className="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center">
                  <MessageCircle className="w-6 h-6 text-white" />
                </div>
                <h4 className="font-semibold text-white">Live Chat</h4>
                <p className="text-sm text-gray-400">Chat with our support team in real-time</p>
                <button className="text-blue-400 hover:text-blue-300 text-sm font-medium">
                  Start Chat
                </button>
              </div>
              
              <div className="space-y-3">
                <div className="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mx-auto flex items-center justify-center">
                  <Mail className="w-6 h-6 text-white" />
                </div>
                <h4 className="font-semibold text-white">Email Support</h4>
                <p className="text-sm text-gray-400">Get detailed help via email</p>
                <a 
                  href="mailto:support@vinreporting.com" 
                  className="text-blue-400 hover:text-blue-300 text-sm font-medium"
                >
                  support@vinreporting.com
                </a>
              </div>
              
              <div className="space-y-3">
                <div className="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mx-auto flex items-center justify-center">
                  <Phone className="w-6 h-6 text-white" />
                </div>
                <h4 className="font-semibold text-white">Phone Support</h4>
                <p className="text-sm text-gray-400">Speak directly with our team</p>
                <a 
                  href="tel:1-800-VIN-REPORT" 
                  className="text-blue-400 hover:text-blue-300 text-sm font-medium"
                >
                  1-800-VIN-REPORT
                </a>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default FAQPage;