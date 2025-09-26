import React, { useState } from 'react';
import { Card } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { Badge } from '../components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '../components/ui/tabs';
import { 
  CheckCircle, 
  AlertTriangle, 
  Car, 
  Calendar, 
  MapPin, 
  Wrench, 
  Users, 
  DollarSign,
  FileText,
  ArrowRight,
  Shield
} from 'lucide-react';
import { Link } from 'react-router-dom';

const SampleReportPage = () => {
  const [activeProvider, setActiveProvider] = useState('carfax');

  const sampleData = {
    carfax: {
      vin: '1HGBH41JXMN109186',
      year: '2021',
      make: 'Honda',
      model: 'Accord Sport',
      color: 'Radiant Red Metallic',
      engine: '1.5L 4-Cylinder Turbo',
      transmission: 'CVT',
      fuel: 'Gasoline',
      mileage: '28,450',
      titleInfo: 'Clean Title',
      accidents: 0,
      owners: 1,
      serviceRecords: 8,
      recalls: 0,
      estimatedValue: '$24,750 - $27,200'
    },
    autocheck: {
      vin: '2HKRM4H75NH200587',
      year: '2022',
      make: 'Honda',
      model: 'Pilot EX-L',
      color: 'Crystal Black Pearl',
      engine: '3.5L V6',
      transmission: '9-Speed Automatic',
      fuel: 'Gasoline',
      mileage: '15,200',
      titleInfo: 'Clean Title',
      accidents: 0,
      owners: 1,
      serviceRecords: 4,
      recalls: 1,
      estimatedValue: '$38,900 - $42,100'
    }
  };

  const currentData = sampleData[activeProvider];

  const serviceHistory = [
    {
      date: '2023-08-15',
      mileage: '25,000',
      type: 'Regular Maintenance',
      location: 'Honda Service Center - Austin, TX',
      description: 'Oil change, tire rotation, multi-point inspection'
    },
    {
      date: '2023-05-10',
      mileage: '20,000',
      type: 'Warranty Service',
      location: 'Honda Dealership - Dallas, TX',
      description: 'Software update, brake inspection'
    },
    {
      date: '2023-01-22',
      mileage: '15,000',
      type: 'Regular Maintenance',
      location: 'QuickLube Plus - Houston, TX',
      description: 'Oil and filter change'
    },
    {
      date: '2022-10-08',
      mileage: '10,000',
      type: 'Regular Maintenance',
      location: 'Honda Service Center - Austin, TX',
      description: 'First scheduled maintenance, all systems check'
    }
  ];

  return (
    <div className="min-h-screen pt-20 pb-12">
      <div className="container mx-auto px-4">
        <div className="max-w-6xl mx-auto">
          
          {/* Header */}
          <div className="text-center mb-12">
            <h1 className="text-4xl lg:text-5xl font-bold text-white mb-4">
              Sample Vehicle History Report
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto mb-8">
              See exactly what you'll receive with our comprehensive vehicle history reports. 
              Switch between Carfax and AutoCheck samples below.
            </p>
            
            {/* Provider Selector */}
            <div className="flex justify-center mb-8">
              <div className="glass p-2 rounded-lg flex space-x-2">
                <Button
                  onClick={() => setActiveProvider('carfax')}
                  className={`px-6 py-2 rounded-md transition-all ${
                    activeProvider === 'carfax'
                      ? 'bg-red-500 text-white'
                      : 'bg-transparent text-gray-400 hover:text-white hover:bg-white/10'
                  }`}
                >
                  Carfax Sample
                </Button>
                <Button
                  onClick={() => setActiveProvider('autocheck')}
                  className={`px-6 py-2 rounded-md transition-all ${
                    activeProvider === 'autocheck'
                      ? 'bg-red-500 text-white'
                      : 'bg-transparent text-gray-400 hover:text-white hover:bg-white/10'
                  }`}
                >
                  AutoCheck Sample
                </Button>
              </div>
            </div>
          </div>

          {/* Report Header */}
          <Card className="glass-card mb-8">
            <div className="flex items-center justify-between mb-6">
              <div className="flex items-center space-x-4">
                <div className="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-500 rounded-lg flex items-center justify-center">
                  <FileText className="w-8 h-8 text-white" />
                </div>
                <div>
                  <h2 className="text-2xl font-bold text-white">Vehicle History Report</h2>
                  <p className="text-gray-400 capitalize">Powered by {activeProvider}</p>
                </div>
              </div>
              <Badge className="bg-green-500/20 text-green-400 border-green-500/30 text-lg px-4 py-2">
                <CheckCircle className="w-5 h-5 mr-2" />
                Clean Title
              </Badge>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div className="text-center">
                <div className="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                  <Car className="w-6 h-6 text-blue-400" />
                </div>
                <p className="text-sm text-gray-400">Vehicle</p>
                <p className="text-white font-semibold">{currentData.year} {currentData.make} {currentData.model}</p>
              </div>
              
              <div className="text-center">
                <div className="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                  <CheckCircle className="w-6 h-6 text-green-400" />
                </div>
                <p className="text-sm text-gray-400">Accidents</p>
                <p className="text-white font-semibold">{currentData.accidents} Reported</p>
              </div>
              
              <div className="text-center">
                <div className="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                  <Users className="w-6 h-6 text-purple-400" />
                </div>
                <p className="text-sm text-gray-400">Previous Owners</p>
                <p className="text-white font-semibold">{currentData.owners} Owner</p>
              </div>
              
              <div className="text-center">
                <div className="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                  <Wrench className="w-6 h-6 text-yellow-400" />
                </div>
                <p className="text-sm text-gray-400">Service Records</p>
                <p className="text-white font-semibold">{currentData.serviceRecords} Records</p>
              </div>
            </div>
          </Card>

          {/* Vehicle Details */}
          <Card className="glass-card mb-8">
            <h3 className="text-xl font-bold text-white mb-6">Vehicle Specifications</h3>
            
            <div className="grid md:grid-cols-2 gap-8">
              <div className="space-y-4">
                <div className="bg-white/5 rounded-lg p-4">
                  <h4 className="font-semibold text-white mb-3">Basic Information</h4>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-gray-400">VIN:</span>
                      <span className="text-white font-mono">{currentData.vin}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Year:</span>
                      <span className="text-white">{currentData.year}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Make/Model:</span>
                      <span className="text-white">{currentData.make} {currentData.model}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Color:</span>
                      <span className="text-white">{currentData.color}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div className="space-y-4">
                <div className="bg-white/5 rounded-lg p-4">
                  <h4 className="font-semibold text-white mb-3">Technical Specs</h4>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-gray-400">Engine:</span>
                      <span className="text-white">{currentData.engine}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Transmission:</span>
                      <span className="text-white">{currentData.transmission}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Fuel Type:</span>
                      <span className="text-white">{currentData.fuel}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-400">Mileage:</span>
                      <span className="text-white">{currentData.mileage} miles</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Card>

          {/* Detailed Sections */}
          <Tabs defaultValue="title" className="w-full">
            <TabsList className="grid w-full grid-cols-5 glass">
              <TabsTrigger value="title" className="text-white data-[state=active]:bg-red-500">Title Info</TabsTrigger>
              <TabsTrigger value="accidents" className="text-white data-[state=active]:bg-red-500">Accidents</TabsTrigger>
              <TabsTrigger value="service" className="text-white data-[state=active]:bg-red-500">Service</TabsTrigger>
              <TabsTrigger value="ownership" className="text-white data-[state=active]:bg-red-500">Ownership</TabsTrigger>
              <TabsTrigger value="value" className="text-white data-[state=active]:bg-red-500">Market Value</TabsTrigger>
            </TabsList>

            <TabsContent value="title" className="mt-6">
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-4">Title Information</h3>
                <div className="space-y-4">
                  <div className="flex items-center space-x-3 p-4 bg-green-500/10 rounded-lg border border-green-500/20">
                    <CheckCircle className="w-6 h-6 text-green-400" />
                    <div>
                      <p className="text-green-400 font-semibold">Clean Title</p>
                      <p className="text-sm text-gray-400">No title issues reported</p>
                    </div>
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="bg-white/5 rounded-lg p-4">
                      <h4 className="font-semibold text-white mb-2">Title History</h4>
                      <ul className="space-y-1 text-sm">
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          Original title issued
                        </li>
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          No lien records found
                        </li>
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          No salvage/flood history
                        </li>
                      </ul>
                    </div>
                    
                    <div className="bg-white/5 rounded-lg p-4">
                      <h4 className="font-semibold text-white mb-2">Registration</h4>
                      <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                          <span className="text-gray-400">Current State:</span>
                          <span className="text-white">Texas</span>
                        </div>
                        <div className="flex justify-between">
                          <span className="text-gray-400">Registration Type:</span>
                          <span className="text-white">Personal Use</span>
                        </div>
                        <div className="flex justify-between">
                          <span className="text-gray-400">Expiration:</span>
                          <span className="text-white">03/2025</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </Card>
            </TabsContent>

            <TabsContent value="accidents" className="mt-6">
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-4">Accident History</h3>
                <div className="text-center py-12">
                  <div className="w-20 h-20 bg-green-500/20 rounded-full mx-auto flex items-center justify-center mb-4">
                    <Shield className="w-10 h-10 text-green-400" />
                  </div>
                  <h4 className="text-2xl font-bold text-white mb-2">No Accidents Reported</h4>
                  <p className="text-gray-400 max-w-md mx-auto">
                    Great news! This vehicle has a clean accident history with no reported collisions, 
                    damage, or insurance claims in our database.
                  </p>
                </div>
              </Card>
            </TabsContent>

            <TabsContent value="service" className="mt-6">
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-6">Service History</h3>
                <div className="space-y-4">
                  {serviceHistory.map((service, index) => (
                    <div key={index} className="bg-white/5 rounded-lg p-4 border-l-4 border-blue-500">
                      <div className="flex justify-between items-start mb-2">
                        <div className="flex items-center space-x-3">
                          <Calendar className="w-5 h-5 text-blue-400" />
                          <div>
                            <p className="text-white font-semibold">{service.type}</p>
                            <p className="text-sm text-gray-400">{service.date} • {service.mileage} miles</p>
                          </div>
                        </div>
                        <Badge variant="outline" className="border-blue-500/30 text-blue-400">
                          Verified
                        </Badge>
                      </div>
                      <div className="ml-8">
                        <p className="text-gray-300 text-sm mb-1">{service.description}</p>
                        <div className="flex items-center text-xs text-gray-400">
                          <MapPin className="w-3 h-3 mr-1" />
                          {service.location}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </Card>
            </TabsContent>

            <TabsContent value="ownership" className="mt-6">
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-4">Ownership History</h3>
                <div className="space-y-6">
                  <div className="bg-white/5 rounded-lg p-6">
                    <div className="flex items-center space-x-4 mb-4">
                      <div className="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                        <Users className="w-6 h-6 text-green-400" />
                      </div>
                      <div>
                        <h4 className="text-lg font-semibold text-white">Single Owner Vehicle</h4>
                        <p className="text-gray-400">Owned since new</p>
                      </div>
                    </div>
                    
                    <div className="grid md:grid-cols-2 gap-4">
                      <div>
                        <h5 className="font-semibold text-white mb-2">Current Owner</h5>
                        <div className="space-y-1 text-sm">
                          <div className="flex justify-between">
                            <span className="text-gray-400">Purchase Date:</span>
                            <span className="text-white">06/15/2021</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-gray-400">Purchase Type:</span>
                            <span className="text-white">New from Dealer</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-gray-400">Location:</span>
                            <span className="text-white">Texas</span>
                          </div>
                        </div>
                      </div>
                      
                      <div>
                        <h5 className="font-semibold text-white mb-2">Usage Type</h5>
                        <div className="space-y-1 text-sm">
                          <div className="flex justify-between">
                            <span className="text-gray-400">Primary Use:</span>
                            <span className="text-white">Personal</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-gray-400">Fleet Vehicle:</span>
                            <span className="text-white">No</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-gray-400">Rental History:</span>
                            <span className="text-white">No</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </Card>
            </TabsContent>

            <TabsContent value="value" className="mt-6">
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-4">Market Value Analysis</h3>
                <div className="grid md:grid-cols-2 gap-6">
                  <div className="bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-lg p-6 border border-green-500/20">
                    <div className="flex items-center space-x-3 mb-4">
                      <DollarSign className="w-8 h-8 text-green-400" />
                      <div>
                        <h4 className="text-xl font-bold text-white">Estimated Value</h4>
                        <p className="text-gray-400">Based on current market conditions</p>
                      </div>
                    </div>
                    
                    <div className="text-center">
                      <p className="text-3xl font-bold text-green-400 mb-2">{currentData.estimatedValue}</p>
                      <p className="text-sm text-gray-400">Private party value range</p>
                    </div>
                  </div>
                  
                  <div className="space-y-4">
                    <div className="bg-white/5 rounded-lg p-4">
                      <h5 className="font-semibold text-white mb-2">Value Factors</h5>
                      <ul className="space-y-2 text-sm">
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          Clean accident history
                        </li>
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          Regular maintenance
                        </li>
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          Single owner
                        </li>
                        <li className="flex items-center text-gray-300">
                          <CheckCircle className="w-4 h-4 text-green-400 mr-2" />
                          No title issues
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </Card>
            </TabsContent>
          </Tabs>

          {/* Call to Action */}
          <Card className="glass-card text-center mt-12">
            <div className="space-y-6">
              <h3 className="text-2xl font-bold text-white">Get Your Own Detailed Report</h3>
              <p className="text-gray-400 max-w-2xl mx-auto">
                This is just a sample of what you'll receive. Get a comprehensive vehicle history report 
                for any vehicle with our instant delivery service.
              </p>
              
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Button 
                  asChild
                  className="bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white border-0 px-8 py-3"
                >
                  <Link to="/">
                    Get My Report Now
                    <ArrowRight className="ml-2 w-4 h-4" />
                  </Link>
                </Button>
                
                <Button 
                  asChild
                  variant="outline"
                  className="border-white/20 text-white hover:bg-white/10 px-8 py-3"
                >
                  <Link to="/">
                    Learn More
                  </Link>
                </Button>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default SampleReportPage;