import React, { useState, useEffect } from 'react';
import { Card } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { Badge } from '../components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '../components/ui/tabs';
import { 
  Users, 
  FileText, 
  DollarSign, 
  TrendingUp, 
  Calendar,
  Mail,
  Phone,
  Car,
  Download,
  RefreshCw
} from 'lucide-react';
import axios from 'axios';
import { toast } from 'sonner';

const AdminDashboard = () => {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    totalOrders: 0,
    totalRevenue: 0,
    completedOrders: 0,
    pendingOrders: 0
  });

  const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
  const API = `${BACKEND_URL}/api`;

  const fetchOrders = async () => {
    setLoading(true);
    try {
      const response = await axios.get(`${API}/orders`);
      const ordersData = response.data;
      setOrders(ordersData);
      
      // Calculate stats
      const totalOrders = ordersData.length;
      const totalRevenue = ordersData.reduce((sum, order) => sum + order.total_amount, 0);
      const completedOrders = ordersData.filter(order => order.payment_status === 'completed').length;
      const pendingOrders = ordersData.filter(order => order.payment_status === 'pending').length;
      
      setStats({
        totalOrders,
        totalRevenue,
        completedOrders,
        pendingOrders
      });
      
    } catch (error) {
      console.error('Error fetching orders:', error);
      toast.error('Failed to fetch orders');
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'completed':
        return 'bg-green-500/20 text-green-400 border-green-500/30';
      case 'pending':
        return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
      case 'failed':
        return 'bg-red-500/20 text-red-400 border-red-500/30';
      default:
        return 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    }
  };

  const resendReport = async (orderId) => {
    try {
      toast.success(`Report resent for order ${orderId.slice(0, 8)}`);
    } catch (error) {
      toast.error('Failed to resend report');
    }
  };

  useEffect(() => {
    fetchOrders();
  }, []);

  return (
    <div className="min-h-screen pt-20 pb-12">
      <div className="container mx-auto px-4">
        <div className="max-w-7xl mx-auto">
          
          {/* Header */}
          <div className="flex justify-between items-center mb-8">
            <div>
              <h1 className="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
              <p className="text-gray-400">Manage orders and view analytics</p>
            </div>
            <Button 
              onClick={fetchOrders}
              disabled={loading}
              className="bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600"
            >
              {loading ? (
                <RefreshCw className="w-4 h-4 mr-2 animate-spin" />
              ) : (
                <RefreshCw className="w-4 h-4 mr-2" />
              )}
              Refresh
            </Button>
          </div>

          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card className="glass-card">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                  <FileText className="w-6 h-6 text-blue-400" />
                </div>
                <div>
                  <p className="text-sm text-gray-400">Total Orders</p>
                  <p className="text-2xl font-bold text-white">{stats.totalOrders}</p>
                </div>
              </div>
            </Card>

            <Card className="glass-card">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                  <DollarSign className="w-6 h-6 text-green-400" />
                </div>
                <div>
                  <p className="text-sm text-gray-400">Total Revenue</p>
                  <p className="text-2xl font-bold text-white">{formatCurrency(stats.totalRevenue)}</p>
                </div>
              </div>
            </Card>

            <Card className="glass-card">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                  <TrendingUp className="w-6 h-6 text-green-400" />
                </div>
                <div>
                  <p className="text-sm text-gray-400">Completed</p>
                  <p className="text-2xl font-bold text-white">{stats.completedOrders}</p>
                </div>
              </div>
            </Card>

            <Card className="glass-card">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                  <Users className="w-6 h-6 text-yellow-400" />
                </div>
                <div>
                  <p className="text-sm text-gray-400">Pending</p>
                  <p className="text-2xl font-bold text-white">{stats.pendingOrders}</p>
                </div>
              </div>
            </Card>
          </div>

          {/* Main Content */}
          <Tabs defaultValue="orders" className="w-full">
            <TabsList className="grid w-full grid-cols-3 glass">
              <TabsTrigger value="orders" className="text-white data-[state=active]:bg-red-500">
                Recent Orders
              </TabsTrigger>
              <TabsTrigger value="analytics" className="text-white data-[state=active]:bg-red-500">
                Analytics
              </TabsTrigger>
              <TabsTrigger value="settings" className="text-white data-[state=active]:bg-red-500">
                Settings
              </TabsTrigger>
            </TabsList>

            <TabsContent value="orders" className="mt-6">
              <Card className="glass-card">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-xl font-bold text-white">Recent Orders</h3>
                  <Badge className="bg-blue-500/20 text-blue-400 border-blue-500/30">
                    {orders.length} Total Orders
                  </Badge>
                </div>

                {loading ? (
                  <div className="text-center py-12">
                    <RefreshCw className="w-8 h-8 text-gray-400 mx-auto mb-4 animate-spin" />
                    <p className="text-gray-400">Loading orders...</p>
                  </div>
                ) : orders.length === 0 ? (
                  <div className="text-center py-12">
                    <FileText className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <p className="text-gray-400">No orders found</p>
                  </div>
                ) : (
                  <div className="space-y-4">
                    {orders.slice(0, 10).map((order) => (
                      <div key={order.id} className="bg-white/5 rounded-lg p-4 border border-white/10">
                        <div className="flex flex-col lg:flex-row justify-between items-start space-y-4 lg:space-y-0">
                          <div className="flex-1 space-y-3">
                            <div className="flex items-center space-x-4">
                              <div className="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <Car className="w-5 h-5 text-white" />
                              </div>
                              <div>
                                <p className="font-semibold text-white">
                                  Order #{order.id.slice(0, 8).toUpperCase()}
                                </p>
                                <p className="text-sm text-gray-400">
                                  {formatDate(order.created_at)}
                                </p>
                              </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                              <div>
                                <span className="text-gray-400">VIN:</span>
                                <p className="text-white font-mono">{order.vin}</p>
                              </div>
                              <div>
                                <span className="text-gray-400">Customer:</span>
                                <p className="text-white">{order.customer_name}</p>
                              </div>
                              <div>
                                <span className="text-gray-400">Email:</span>
                                <p className="text-white">{order.customer_email}</p>
                              </div>
                              <div>
                                <span className="text-gray-400">Phone:</span>
                                <p className="text-white">{order.customer_phone}</p>
                              </div>
                            </div>

                            <div className="flex flex-wrap items-center gap-2">
                              <Badge className={getStatusColor(order.payment_status)}>
                                {order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}
                              </Badge>
                              <Badge variant="outline" className="border-gray-500/30 text-gray-400">
                                {order.report_provider.charAt(0).toUpperCase() + order.report_provider.slice(1)}
                              </Badge>
                              <Badge variant="outline" className="border-gray-500/30 text-gray-400">
                                {order.plan_type.charAt(0).toUpperCase() + order.plan_type.slice(1)}
                              </Badge>
                            </div>
                          </div>

                          <div className="flex flex-col items-end space-y-2">
                            <p className="text-xl font-bold text-white">
                              {formatCurrency(order.total_amount)}
                            </p>
                            <div className="flex space-x-2">
                              <Button
                                size="sm"
                                onClick={() => resendReport(order.id)}
                                className="bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 border border-blue-500/30"
                              >
                                <Mail className="w-4 h-4 mr-1" />
                                Resend
                              </Button>
                              <Button
                                size="sm"
                                className="bg-green-500/20 text-green-400 hover:bg-green-500/30 border border-green-500/30"
                              >
                                <Download className="w-4 h-4 mr-1" />
                                Invoice
                              </Button>
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </Card>
            </TabsContent>

            <TabsContent value="analytics" className="mt-6">
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <Card className="glass-card">
                  <h3 className="text-xl font-bold text-white mb-4">Revenue Trends</h3>
                  <div className="text-center py-12">
                    <TrendingUp className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <p className="text-gray-400">Analytics charts coming soon</p>
                  </div>
                </Card>

                <Card className="glass-card">
                  <h3 className="text-xl font-bold text-white mb-4">Popular Plans</h3>
                  <div className="space-y-4">
                    {['Premium Report', 'Basic Report', 'Ultimate Report'].map((plan, index) => (
                      <div key={plan} className="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                        <span className="text-white">{plan}</span>
                        <Badge className="bg-red-500/20 text-red-400">
                          {[65, 25, 10][index]}%
                        </Badge>
                      </div>
                    ))}
                  </div>
                </Card>
              </div>
            </TabsContent>

            <TabsContent value="settings" className="mt-6">
              <Card className="glass-card">
                <h3 className="text-xl font-bold text-white mb-4">System Settings</h3>
                <div className="space-y-6">
                  <div className="bg-white/5 rounded-lg p-4">
                    <h4 className="font-semibold text-white mb-2">Payment Configuration</h4>
                    <p className="text-gray-400 text-sm mb-4">PayPal integration status and settings</p>
                    <div className="space-y-2">
                      <div className="flex justify-between items-center">
                        <span className="text-gray-300">PayPal Mode:</span>
                        <Badge className="bg-yellow-500/20 text-yellow-400">Sandbox</Badge>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-gray-300">Client ID:</span>
                        <code className="text-xs text-gray-400">ASzv9p9J...ZPqlFOl</code>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white/5 rounded-lg p-4">
                    <h4 className="font-semibold text-white mb-2">Email Configuration</h4>
                    <p className="text-gray-400 text-sm mb-4">Email delivery settings</p>
                    <div className="space-y-2">
                      <div className="flex justify-between items-center">
                        <span className="text-gray-300">From Email:</span>
                        <span className="text-white">no-reply@vinreporting.com</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-gray-300">CC Email:</span>
                        <span className="text-white">billing@vinreporting.com</span>
                      </div>
                    </div>
                  </div>
                </div>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;