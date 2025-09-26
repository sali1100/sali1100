import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import "./App.css";

// Import pages
import HomePage from "./pages/HomePage";
import CheckoutPage from "./pages/CheckoutPage";
import ThankYouPage from "./pages/ThankYouPage";
import SampleReportPage from "./pages/SampleReportPage";
import AdminLogin from "./pages/AdminLogin";
import AdminDashboard from "./pages/AdminDashboard";
import PricingPage from "./pages/PricingPage";
import FAQPage from "./pages/FAQPage";

// Import service pages
import VehicleHistoryReports from "./pages/services/VehicleHistoryReports";
import AccidentHistoryCheck from "./pages/services/AccidentHistoryCheck";
import TitleInformation from "./pages/services/TitleInformation";
import ServiceRecords from "./pages/services/ServiceRecords";
import MarketValueAnalysis from "./pages/services/MarketValueAnalysis";
import RecallInformation from "./pages/services/RecallInformation";

// Import components
import Header from "./components/Header";
import Footer from "./components/Footer";
import { Toaster } from "./components/ui/sonner";
import ProtectedRoute from "./components/ProtectedRoute";

function App() {
  return (
    <div className="App">
      <BrowserRouter>
        <Header />
        <main className="min-h-screen">
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/checkout" element={<CheckoutPage />} />
            <Route path="/thank-you" element={<ThankYouPage />} />
            <Route path="/sample-report" element={<SampleReportPage />} />
            <Route path="/pricing" element={<PricingPage />} />
            <Route path="/faq" element={<FAQPage />} />
            
            {/* Service Pages */}
            <Route path="/services/vehicle-history-reports" element={<VehicleHistoryReports />} />
            <Route path="/services/accident-history-check" element={<AccidentHistoryCheck />} />
            <Route path="/services/title-information" element={<TitleInformation />} />
            <Route path="/services/service-records" element={<ServiceRecords />} />
            <Route path="/services/market-value-analysis" element={<MarketValueAnalysis />} />
            <Route path="/services/recall-information" element={<RecallInformation />} />
            
            {/* Admin Routes */}
            <Route path="/admin/login" element={<AdminLogin />} />
            <Route path="/admin" element={
              <ProtectedRoute>
                <AdminDashboard />
              </ProtectedRoute>
            } />
          </Routes>
        </main>
        <Footer />
        <Toaster />
      </BrowserRouter>
    </div>
  );
}

export default App;