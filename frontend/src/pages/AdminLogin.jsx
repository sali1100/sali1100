import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { Lock, Eye, EyeOff } from 'lucide-react';
import { toast } from 'sonner';

const AdminLogin = () => {
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();

  // Admin password hash (in production, this should be stored more securely)
  const ADMIN_PASSWORD_HASH = 'admin123'; // Change this to your desired admin password

  const handleLogin = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    // Simple password check (in production, use proper hashing)
    if (password === ADMIN_PASSWORD_HASH) {
      localStorage.setItem('adminAuthenticated', 'true');
      toast.success('Login successful!');
      navigate('/admin');
    } else {
      toast.error('Invalid password. Please try again.');
    }
    
    setIsLoading(false);
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
        <div className="max-w-md mx-auto">
          <div className="text-center mb-8">
            <div className="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center mb-4">
              <Lock className="w-8 h-8 text-white" />
            </div>
            <h1 className="text-3xl font-bold text-white mb-2">Admin Login</h1>
            <p className="text-gray-400">Enter your password to access the admin dashboard</p>
          </div>

          <Card className="glass-card">
            <form onSubmit={handleLogin} className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-300 mb-2">
                  Admin Password
                </label>
                <div className="relative">
                  <input
                    type={showPassword ? 'text' : 'password'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Enter admin password"
                    className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 pr-12"
                    required
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                  >
                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                  </button>
                </div>
              </div>

              <Button
                type="submit"
                disabled={isLoading || !password}
                className="w-full py-3 text-lg font-semibold bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white border-0"
                data-testid="admin-login-btn"
              >
                {isLoading ? (
                  <div className="flex items-center space-x-2">
                    <div className="spinner"></div>
                    <span>Logging in...</span>
                  </div>
                ) : (
                  'Login to Admin Panel'
                )}
              </Button>
            </form>

            <div className="mt-6 p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
              <p className="text-sm text-blue-400 text-center">
                🔒 Secure admin access - unauthorized access is prohibited
              </p>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default AdminLogin;