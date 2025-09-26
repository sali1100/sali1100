import React from 'react';
import { Link } from 'react-router-dom';
import { Card } from '../../components/ui/card';
import { Button } from '../../components/ui/button';
import { Badge } from '../../components/ui/badge';
import { Wrench, CheckCircle, Calendar, ArrowRight } from 'lucide-react';

const ServiceRecords = () => {
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
            <Badge className="bg-gradient-to-r from-green-500/20 to-blue-500/20 text-green-400 border-green-500/30 mb-6">
              🔧 Maintenance History
            </Badge>
            <h1 className="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-6">
              Service Records
            </h1>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
              Access detailed maintenance and service history to understand how well 
              a vehicle has been cared for throughout its lifetime.
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8 mb-16">
            <Card className="glass-card text-center">
              <Wrench className="w-12 h-12 text-blue-400 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-white mb-2">Maintenance Records</h3>
              <p className="text-gray-400 text-sm">
                Oil changes, brake work, tire rotations, and scheduled maintenance
              </p>
            </Card>

            <Card className="glass-card text-center">
              <Calendar className="w-12 h-12 text-green-400 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-white mb-2">Service Timeline</h3>
              <p className="text-gray-400 text-sm">
                Complete chronological history of all documented service events
              </p>
            </Card>

            <Card className="glass-card text-center">
              <CheckCircle className="w-12 h-12 text-purple-400 mx-auto mb-4" />
              <h3 className="text-lg font-bold text-white mb-2">Quality Indicators</h3>
              <p className="text-gray-400 text-sm">
                Well-maintained vehicles have fewer problems and higher value
              </p>
            </Card>
          </div>

          <Card className="glass-card text-center">
            <h3 className="text-2xl font-bold text-white mb-4">View Complete Service History</h3>
            <p className="text-gray-400 mb-6">
              Make informed decisions based on documented maintenance and care.
            </p>
            <Button 
              asChild
              className="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0"
            >
              <Link to="/">
                Get Service Records
                <ArrowRight className="ml-2 w-4 h-4" />
              </Link>
            </Button>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default ServiceRecords;