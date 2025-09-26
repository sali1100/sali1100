import React from 'react';
import { Link } from 'react-router-dom';
import { Card } from '../../components/ui/card';
import { Button } from '../../components/ui/button';
import { Badge } from '../../components/ui/badge';
import { AlertTriangle, Shield, CheckCircle, ArrowRight, Car, FileText } from 'lucide-react';

const AccidentHistoryCheck = () => {
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
            <Badge className="bg-gradient-to-r from-red-500/20 to-orange-500/20 text-red-400 border-red-500/30 mb-6">
              🚨 Accident Detection
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Accident History Check
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Discover any accidents, collisions, or damage events in a vehicle's history. 
              Our comprehensive accident checks help you avoid costly surprises.
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-8 mb-16">
            <Card className="glass-card">
              <AlertTriangle className="w-12 h-12 text-red-400 mb-4" />
              <h3 className="text-xl font-bold text-white mb-4">What We Detect</h3>
              <ul className="space-y-2">
                {[
                  "Major and minor collision damage",
                  "Insurance claims and payouts", 
                  "Airbag deployment incidents",
                  "Structural damage reports",
                  "Hail and weather damage",
                  "Vandalism and theft damage"
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
              <h3 className="text-xl font-bold text-white mb-4">Why It Matters</h3>
              <p className="text-gray-300 mb-4">
                Accident history significantly impacts vehicle safety, performance, and value. 
                Even minor accidents can lead to ongoing mechanical issues if not properly repaired.
              </p>
              <ul className="space-y-2">
                {[
                  "Affects resale value",
                  "Safety implications", 
                  "Insurance considerations",
                  "Future reliability"
                ].map((item, index) => (
                  <li key={index} className="flex items-center space-x-2">
                    <AlertTriangle className="w-4 h-4 text-yellow-400" />
                    <span className="text-gray-300 text-sm">{item}</span>
                  </li>
                ))}
              </ul>
            </Card>
          </div>

          <Card className="glass-card text-center">
            <h3 className="text-2xl font-bold text-white mb-4">Get Your Accident History Report</h3>
            <p className="text-gray-400 mb-6">
              Protect yourself from hidden accident damage with our comprehensive checks.
            </p>
            <Button 
              asChild
              className="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0"
            >
              <Link to="/">
                Check Vehicle History
                <ArrowRight className="ml-2 w-4 h-4" />
              </Link>
            </Button>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default AccidentHistoryCheck;