import React from 'react';
import { Link } from 'react-router-dom';
import { Card } from '../../components/ui/card';
import { Button } from '../../components/ui/button';
import { Badge } from '../../components/ui/badge';
import { DollarSign, TrendingUp, BarChart, ArrowRight } from 'lucide-react';

const MarketValueAnalysis = () => {
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
            <Badge className="bg-gradient-to-r from-yellow-500/20 to-green-500/20 text-yellow-400 border-yellow-500/30 mb-6">
              💰 Market Analysis
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Market Value Analysis
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Get accurate market valuations and pricing insights to ensure you're 
              getting a fair deal on your vehicle purchase or sale.
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8 mb-16">
            <Card className="glass-card text-center">
              <DollarSign className="w-12 h-12 text-green-400 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-white mb-2">Current Value</h3>
              <p className="text-gray-400 text-sm">
                Real-time market pricing based on condition, mileage, and location
              </p>
            </Card>

            <Card className="glass-card text-center">
              <TrendingUp className="w-12 h-12 text-blue-400 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-white mb-2">Price Trends</h3>
              <p className="text-gray-400 text-sm">
                Historical pricing data and future value projections
              </p>
            </Card>

            <Card className="glass-card text-center">
              <BarChart className="w-12 h-12 text-purple-400 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-white mb-2">Market Comparison</h3>
              <p className="text-gray-400 text-sm">
                Compare similar vehicles in your area for negotiation power
              </p>
            </Card>
          </div>

          <Card className="glass-card text-center">
            <h3 className="text-2xl font-bold text-white mb-4">Get Market Value Analysis</h3>
            <p className="text-gray-400 mb-6">
              Know what a vehicle is really worth before you buy or sell.
            </p>
            <Button 
              asChild
              className="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0"
            >
              <Link to="/">
                Analyze Market Value
                <ArrowRight className="ml-2 w-4 h-4" />
              </Link>
            </Button>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default MarketValueAnalysis;