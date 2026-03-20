import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AppShell } from './components/layout/AppShell';
import Login from './pages/auth/Login';
import BuyerDashboard from './pages/buyer/Dashboard';
import Landing from './pages/Landing';
import Products from './pages/buyer/Products';
import ProductDetail from './pages/buyer/ProductDetail';
import Orders from './pages/buyer/Orders';
import OrderDetail from './pages/buyer/OrderDetail';
import RFQNew from './pages/buyer/RFQNew';
import './styles/globals.css';
import '../../css/app.css';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Landing />} />
        <Route path="/login" element={<Login />} />

        <Route element={<AppShell />}>
          <Route path="/dashboard" element={<BuyerDashboard />} />
          <Route path="/products" element={<Products />} />
          <Route path="/products/:id" element={<ProductDetail />} />
          <Route path="/orders" element={<Orders />} />
          <Route path="/orders/:id" element={<OrderDetail />} />
          <Route path="/rfq/new" element={<RFQNew />} />
        </Route>

        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </BrowserRouter>
  );
}

ReactDOM.createRoot(document.getElementById('app')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

