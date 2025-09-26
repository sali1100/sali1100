import React from 'react';
import { Link } from 'react-router-dom';
import { Card } from '../../components/ui/card';
import { Button } from '../../components/ui/button';
import { Badge } from '../../components/ui/badge';
import { FileText, CheckCircle, AlertTriangle, ArrowRight } from 'lucide-react';

const TitleInformation = () => {
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
            <Badge className="bg-gradient-to-r from-blue-500/20 to-green-500/20 text-blue-400 border-blue-500/30 mb-6">
              📋 Title Verification
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Title Information
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Verify vehicle title status and ownership history. Avoid vehicles with 
              problematic titles that could affect your investment.
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-8 mb-16">
            <Card className="glass-card">
              <CheckCircle className="w-12 h-12 text-green-400 mb-4" />
              <h3 className="text-xl font-bold text-white mb-4">Clean Title Benefits</h3>
              <ul className="space-y-2">
                {[
                  "No major damage history",
                  "Higher resale value",
                  "Easier financing options",
                  "Lower insurance rates",
                  "Peace of mind purchase"
                ].map((item, index) => (
                  <li key={index} className="flex items-center space-x-2">
                    <CheckCircle className="w-4 h-4 text-green-400" />
                    <span className="text-gray-300 text-sm">{item}</span>
                  </li>
                ))}
              </ul>
            </Card>

            <Card className="glass-card">
              <AlertTriangle className="w-12 h-12 text-red-400 mb-4" />
              <h3 className="text-xl font-bold text-white mb-4">Title Issues to Avoid</h3>
              <ul className="space-y-2">
                {[
                  "Salvage titles (total loss)",
                  "Flood damage brands",
                  "Lemon law buybacks", 
                  "Fire damage history",
                  "Theft recovery vehicles"
                ].map((item, index) => (
                  <li key={index} className="flex items-center space-x-2">
                    <AlertTriangle className="w-4 h-4 text-red-400" />
                    <span className="text-gray-300 text-sm">{item}</span>
                  </li>
                ))}
              </ul>
            </Card>
          </div>

          <Card className="glass-card text-center">
            <h3 className="text-2xl font-bold text-white mb-4">Verify Title Status Now</h3>
            <p className="text-gray-400 mb-6">
              Don't risk buying a vehicle with title problems. Get the facts before you purchase.
            </p>
            <Button 
              asChild
              className="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0"
            >
              <Link to="/">
                Check Title Information
                <ArrowRight className="ml-2 w-4 h-4" />
              </Link>
            </Button>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default TitleInformation;