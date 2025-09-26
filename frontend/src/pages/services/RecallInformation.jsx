import React from 'react';
import { Link } from 'react-router-dom';
import { Card } from '../../components/ui/card';
import { Button } from '../../components/ui/button';
import { Badge } from '../../components/ui/badge';
import { AlertCircle, Shield, CheckCircle, ArrowRight } from 'lucide-react';

const RecallInformation = () => {
  return (
    <div className="min-h-screen pt-20 pb-12">
      <div className="particles-bg">
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
        <div className="particle"></div>
      </div>

      <div className="container mx-auto px-4">
        <div className="max-w-6xl mx-auto">
          <div className="text-center mb-16">
            <Badge className="bg-gradient-to-r from-red-500/20 to-yellow-500/20 text-red-400 border-red-500/30 mb-6">
              ⚠️ Safety Recalls
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Recall Information
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Stay informed about safety recalls and manufacturer notifications that 
              could affect vehicle safety and performance.
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-8 mb-16">
            <Card className="glass-card">
              <AlertCircle className="w-12 h-12 text-red-400 mb-4" />
              <h3 className="text-xl font-bold text-white mb-4">What We Check</h3>
              <ul className="space-y-2">
                {[
                  "Safety-related recalls",
                  "Technical service bulletins", 
                  "Manufacturer notifications",
                  "NHTSA safety campaigns",
                  "Emission recalls",
                  "Software update notices"
                ].map((item, index) => (
                  <li key={index} className="flex items-center space-x-2">
                    <CheckCircle className="w-4 h-4 text-green-400" />
                    <span className="text-gray-300 text-sm">{item}</span>
                  </li>
                ))}
              </ul>
            </Card>

            <Card className="glass-card">
              <Shield className="w-12 h-12 text-green-400 mb-4" />
              <h3 className="text-xl font-bold text-white mb-4">Why It's Important</h3>
              <p className="text-gray-300 mb-4">
                Unaddressed recalls can pose serious safety risks and may affect 
                vehicle registration, insurance, and resale value.
              </p>
              <ul className="space-y-2">
                {[
                  "Safety protection",
                  "Legal compliance", 
                  "Insurance coverage",
                  "Resale value protection"
                ].map((item, index) => (
                  <li key={index} className="flex items-center space-x-2">
                    <Shield className="w-4 h-4 text-green-400" />
                    <span className="text-gray-300 text-sm">{item}</span>
                  </li>
                ))}
              </ul>
            </Card>
          </div>

          <Card className="glass-card text-center">
            <h3 className="text-2xl font-bold text-white mb-4">Check for Recalls</h3>
            <p className="text-gray-400 mb-6">
              Ensure your safety with up-to-date recall information.
            </p>
            <Button 
              asChild
              className="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0"
            >
              <Link to="/">
                Check Recall Status
                <ArrowRight className="ml-2 w-4 h-4" />
              </Link>
            </Button>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default RecallInformation;