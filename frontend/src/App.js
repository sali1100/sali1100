import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import "./App.css";

// Import pages
import HomePage from "./pages/HomePage";
import CheckoutPage from "./pages/CheckoutPage";
import ThankYouPage from "./pages/ThankYouPage";
import SampleReportPage from "./pages/SampleReportPage";
import AdminDashboard from "./pages/AdminDashboard";

// Import components
import Header from "./components/Header";
import Footer from "./components/Footer";
import { Toaster } from "./components/ui/sonner";

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
            <Route path="/admin" element={<AdminDashboard />} />
          </Routes>
        </main>
        <Footer />
        <Toaster />
      </BrowserRouter>
    </div>
  );
}

export default App;