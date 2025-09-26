import React from 'react';
import { Link } from 'react-router-dom';
import { Card } from '../../components/ui/card';
import { Button } from '../../components/ui/button';
import { Badge } from '../../components/ui/badge';
import { CheckCircle, FileText, Shield, Clock, ArrowRight, Car, Database, Award } from 'lucide-react';

const VehicleHistoryReports = () => {
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
              🚗 Our Primary Service
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Vehicle History Reports
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Get comprehensive, accurate vehicle history reports powered by trusted data sources. 
              Make informed decisions with detailed insights into any vehicle's past.
            </p>
          </div>

          {/* Main Features */}
          <div className="grid md:grid-cols-3 gap-8 mb-16">
            <Card className="glass-card text-center">
              <div className="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center mb-6">
                <Database className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-xl font-bold text-white mb-4">Comprehensive Data</h3>
              <p className="text-gray-400">
                Access millions of records from DMV, insurance companies, service centers, 
                and auction houses for complete vehicle transparency.
              </p>
            </Card>

            <Card className="glass-card text-center">
              <div className="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mx-auto flex items-center justify-center mb-6">
                <Shield className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-xl font-bold text-white mb-4">99.9% Accuracy</h3>
              <p className="text-gray-400">
                Our reports achieve industry-leading accuracy through advanced data 
                verification and cross-referencing multiple trusted sources.
              </p>
            </Card>

            <Card className="glass-card text-center">
              <div className="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mx-auto flex items-center justify-center mb-6">
                <Clock className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-xl font-bold text-white mb-4">Instant Delivery</h3>
              <p className="text-gray-400">
                Receive your detailed vehicle history report immediately via email 
                upon successful payment completion.
              </p>
            </Card>
          </div>

          {/* What's Included */}
          <Card className="glass-card mb-16">
            <div className="grid md:grid-cols-2 gap-8">
              <div>
                <h3 className="text-2xl font-bold text-white mb-6">What's Included in Every Report</h3>
                <div className="space-y-4">
                  {[
                    "Complete accident and damage history",
                    "Title information and branding",
                    "Service and maintenance records",
                    "Previous ownership details",
                    "Mileage verification and rollback detection",
                    "Lemon, flood, and fire damage indicators",
                    "Recall and safety information",
                    "Market value analysis and pricing",
                    "Detailed vehicle specifications",
                    "Registration and inspection history"
                  ].map((feature, index) => (
                    <div key={index} className="flex items-center space-x-3">
                      <CheckCircle className="w-5 h-5 text-green-400 flex-shrink-0" />
                      <span className="text-gray-300">{feature}</span>
                    </div>
                  ))}
                </div>
              </div>
              
              <div className="space-y-6">
                <div className="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 rounded-lg p-6 border border-blue-500/20">
                  <h4 className="font-bold text-white mb-3">Report Sample Preview</h4>
                  <div className="space-y-3 text-sm">
                    <div className="flex justify-between">
                      <span className="text-gray-400">VIN:</span>
                      <span className="text-white">1HGBH41JXMN109186</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Vehicle:</span>
                      <span className="text-white">2021 Honda Accord Sport</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Title Status:</span>
                      <span className="text-green-400">Clean Title</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Accidents:</span>
                      <span className="text-green-400">No accidents reported</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Owners:</span>
                      <span className="text-white">1 Previous Owner</span>
                    </div>
                  </div>
                  <Link to="/sample-report">
                    <Button className="w-full mt-4 bg-transparent border border-blue-500/30 text-blue-400 hover:bg-blue-500/20">
                      View Full Sample Report
                    </Button>
                  </Link>
                </div>

                <div className="space-y-4">
                  <h4 className="font-bold text-white">Trusted Data Sources</h4>
                  <div className="grid grid-cols-2 gap-3">
                    {[
                      "DMV Records",
                      "Insurance Companies", 
                      "Service Centers",
                      "Auction Houses",
                      "Fire Departments",
                      "Police Reports"
                    ].map((source, index) => (
                      <div key={index} className="flex items-center space-x-2">
                        <Award className="w-4 h-4 text-blue-400" />
                        <span className="text-gray-300 text-sm">{source}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          </Card>

          {/* How It Works */}
          <Card className="glass-card mb-16">
            <h3 className="text-2xl font-bold text-white text-center mb-8">How It Works</h3>
            <div className="grid md:grid-cols-4 gap-6">
              {[
                {
                  step: "1",
                  title: "Enter VIN",
                  description: "Input the 17-digit Vehicle Identification Number"
                },
                {
                  step: "2", 
                  title: "Choose Plan",
                  description: "Select your preferred report package and provider"
                },
                {
                  step: "3",
                  title: "Secure Payment",
                  description: "Complete payment with our encrypted checkout"
                },
                {
                  step: "4",
                  title: "Get Report",
                  description: "Receive your comprehensive report via email instantly"
                }
              ].map((item, index) => (
                <div key={index} className="text-center">
                  <div className="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center text-white font-bold text-lg mb-4">
                    {item.step}
                  </div>
                  <h4 className="font-semibold text-white mb-2">{item.title}</h4>
                  <p className="text-gray-400 text-sm">{item.description}</p>
                </div>
              ))}
            </div>
          </Card>

          {/* CTA Section */}
          <Card className="glass-card text-center">
            <h3 className="text-3xl font-bold text-white mb-4">Ready to Get Your Vehicle History Report?</h3>
            <p className="text-gray-400 mb-8 max-w-2xl mx-auto">
              Join over 2 million satisfied customers who trust VinReporting.com for accurate, 
              comprehensive vehicle history information.
            </p>
            
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button 
                asChild
                className="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0 px-8 py-3"
              >
                <Link to="/">
                  Get Your Report Now
                  <ArrowRight className="ml-2 w-4 h-4" />
                </Link>
              </Button>
              
              <Button 
                asChild
                variant="outline"
                className="border-white/20 text-white hover:bg-white/10 px-8 py-3"
              >
                <Link to="/pricing">
                  View Pricing Plans
                </Link>
              </Button>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default VehicleHistoryReports;