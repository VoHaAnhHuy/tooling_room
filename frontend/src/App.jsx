import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './context/AuthContext'
import Layout from './components/layout/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Collaterals from './pages/Collaterals'
import Cabinets from './pages/Cabinets'
import Products from './pages/Products'
import Tickets from './pages/Tickets'

function PrivateRoute({ children }) {
  const { isAuth } = useAuth()
  return isAuth ? children : <Navigate to="/login" replace />
}

function AppRoutes() {
  const { isAuth } = useAuth()
  return (
    <Routes>
      <Route path="/login" element={isAuth ? <Navigate to="/" replace /> : <Login />} />
      <Route path="/" element={<PrivateRoute><Layout><Dashboard /></Layout></PrivateRoute>} />
      <Route path="/collaterals" element={<PrivateRoute><Layout><Collaterals /></Layout></PrivateRoute>} />
      <Route path="/cabinets" element={<PrivateRoute><Layout><Cabinets /></Layout></PrivateRoute>} />
      <Route path="/products" element={<PrivateRoute><Layout><Products /></Layout></PrivateRoute>} />
      <Route path="/tickets" element={<PrivateRoute><Layout><Tickets /></Layout></PrivateRoute>} />
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <AppRoutes />
      </AuthProvider>
    </BrowserRouter>
  )
}
