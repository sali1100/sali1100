import React from 'react';
import { Link } from 'react-router-dom';
import { Car, Mail, Phone, MapPin, Shield, Award, Clock } from 'lucide-react';

const Footer = () => {
  return (
    <footer className="bg-gradient-to-t from-black to-gray-900/50 border-t border-white/10">
      <div className="container mx-auto px-4 py-12">
        <div className="grid md:grid-cols-4 gap-8">
          {/* Company Info */}
          <div className="space-y-4">
            <Link to="/" className="flex items-center space-x-2">
              <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                <Car className="w-6 h-6 text-white" />
              </div>
              <div>
                <span className="text-xl font-bold text-white">VinReporting</span>
                <span className="text-sm text-gray-400 block leading-none">.com</span>
              </div>
            </Link>
            <p className="text-gray-400 text-sm">
              Your trusted source for comprehensive vehicle history reports. 
              Powered by Carfax and AutoCheck data for complete transparency.
            </p>
            <div className="flex space-x-4">
              {[
                { icon: <Shield className="w-5 h-5" />, text: 'Secure' },
                { icon: <Award className="w-5 h-5" />, text: 'Trusted' },
                { icon: <Clock className="w-5 h-5" />, text: 'Instant' }
              ].map((badge, index) => (
                <div key={index} className="flex items-center space-x-1 glass px-2 py-1 rounded-full">
                  <div className="text-blue-400">{badge.icon}</div>
                  <span className="text-xs text-gray-300">{badge.text}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Quick Links */}
          <div className="space-y-4">
            <h4 className="text-lg font-semibold text-white">Quick Links</h4>
            <div className="space-y-2">
              {[
                { name: 'Home', href: '/' },
                { name: 'Sample Report', href: '/sample-report' },
                { name: 'Pricing', href: '/pricing' },
                { name: 'FAQ', href: '/faq' },
                { name: 'Admin Login', href: '/admin/login' }
              ].map((link) => (
                <Link
                  key={link.name}
                  to={link.href}
                  className="block text-gray-400 hover:text-blue-400 transition-colors text-sm"
                >
                  {link.name}
                </Link>
              ))}
            </div>
          </div>

          {/* Services */}
          <div className="space-y-4">
            <h4 className="text-lg font-semibold text-white">Our Services</h4>
            <div className="space-y-2">
              {[
                { name: 'Vehicle History Reports', href: '/services/vehicle-history-reports' },
                { name: 'Accident History Check', href: '/services/accident-history-check' },
                { name: 'Title Information', href: '/services/title-information' },
                { name: 'Service Records', href: '/services/service-records' },
                { name: 'Market Value Analysis', href: '/services/market-value-analysis' },
                { name: 'Recall Information', href: '/services/recall-information' }
              ].map((service) => (
                <Link
                  key={service.name}
                  to={service.href}
                  className="block text-gray-400 hover:text-blue-400 transition-colors text-sm"
                >
                  {service.name}
                </Link>
              ))}
            </div>
          </div>

          {/* Contact Info */}
          <div className="space-y-4">
            <h4 className="text-lg font-semibold text-white">Get in Touch</h4>
            <div className="space-y-3">
              <div className="flex items-center space-x-3">
                <Mail className="w-5 h-5 text-blue-400" />
                <span className="text-gray-400 text-sm">support@vinreporting.com</span>
              </div>
              <div className="flex items-center space-x-3">
                <Phone className="w-5 h-5 text-blue-400" />
                <span className="text-gray-400 text-sm">1-800-VIN-REPORT</span>
              </div>
              <div className="flex items-center space-x-3">
                <MapPin className="w-5 h-5 text-blue-400" />
                <span className="text-gray-400 text-sm">North America</span>
              </div>
            </div>

            {/* Trust Badges */}
            <div className="space-y-2">
              <div className="text-sm text-gray-400">Trusted by:</div>
              <div className="grid grid-cols-2 gap-2 text-xs text-gray-500">
                <div className="glass px-2 py-1 rounded text-center">2M+ Customers</div>
                <div className="glass px-2 py-1 rounded text-center">4.9★ Rating</div>
                <div className="glass px-2 py-1 rounded text-center">SSL Secured</div>
                <div className="glass px-2 py-1 rounded text-center">24/7 Support</div>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="border-t border-white/10 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <div className="text-sm text-gray-400">
            © 2025 VinReporting.com. All rights reserved.
          </div>
          <div className="flex space-x-6 text-sm">
            <Link to="/privacy" className="text-gray-400 hover:text-blue-400 transition-colors">
              Privacy Policy
            </Link>
            <Link to="/terms" className="text-gray-400 hover:text-blue-400 transition-colors">
              Terms of Service
            </Link>
            <Link to="/refund" className="text-gray-400 hover:text-blue-400 transition-colors">
              Refund Policy
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;