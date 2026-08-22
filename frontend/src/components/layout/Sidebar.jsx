import { NavLink, useNavigate } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import {
  LayoutDashboard, Package, Archive, Boxes, Ticket, LogOut, Wrench
} from 'lucide-react'

const navItems = [
  { to: '/', icon: <LayoutDashboard size={18} />, label: 'Dashboard' },
  { to: '/collaterals', icon: <Package size={18} />, label: 'Collaterals' },
  { to: '/cabinets', icon: <Archive size={18} />, label: 'Cabinets' },
  { to: '/products', icon: <Boxes size={18} />, label: 'Products' },
  { to: '/tickets', icon: <Ticket size={18} />, label: 'Tickets' },
]

export default function Sidebar() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  const handleLogout = async () => {
    await logout()
    navigate('/login')
  }

  return (
    <aside className="sidebar">
      <div className="sidebar-brand">
        <div className="sidebar-brand-icon"><Wrench size={18} /></div>
        <div>
          <div className="sidebar-brand-text">Tooling Room</div>
          <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>Asset Management</div>
        </div>
      </div>

      <nav className="sidebar-nav">
        <div className="nav-section-label">Navigation</div>
        {navItems.map(item => (
          <NavLink key={item.to} to={item.to} end={item.to === '/'}
            className={({ isActive }) => `nav-item ${isActive ? 'active' : ''}`}>
            {item.icon}
            {item.label}
          </NavLink>
        ))}
      </nav>

      <div className="sidebar-footer">
        <div style={{ padding: '8px 10px', marginBottom: 6 }}>
          <div style={{ fontSize: 13, fontWeight: 600 }}>{user?.name ?? 'User'}</div>
          <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{user?.email}</div>
        </div>
        <button className="nav-item btn-ghost" style={{ width: '100%', border: 'none', background: 'none' }}
          onClick={handleLogout}>
          <LogOut size={16} /> Sign out
        </button>
      </div>
    </aside>
  )
}
