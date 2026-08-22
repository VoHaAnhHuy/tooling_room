import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { Package, Archive, Boxes, Ticket, Clock, ArrowRight } from 'lucide-react'
import { StatCard, LoadingSpinner } from '../components/ui/Shared'
import { statusBadge } from '../components/ui/Badge'
import { getCollaterals } from '../api/collaterals'
import { getCabinets } from '../api/cabinets'
import { getProducts } from '../api/products'
import { getTickets } from '../api/tickets'

export default function Dashboard() {
  const [stats, setStats] = useState(null)
  const [recentTickets, setRecentTickets] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    Promise.all([
      getCollaterals({ per_page: 100 }),
      getCollaterals({ per_page: 1, status: 'available' }),
      getCollaterals({ per_page: 1, status: 'borrowed' }),
      getCabinets({ per_page: 100 }),
      getProducts({ per_page: 100 }),
      getTickets({ per_page: 5 }),
    ]).then(([all, avail, borrowed, cabs, prods, tickets]) => {
      setStats({
        total: all.data.meta?.total ?? 0,
        available: avail.data.meta?.total ?? 0,
        borrowed: borrowed.data.meta?.total ?? 0,
        cabinets: cabs.data.meta?.total ?? 0,
        products: prods.data.meta?.total ?? 0,
      })
      setRecentTickets(tickets.data.data ?? [])
    }).finally(() => setLoading(false))
  }, [])

  if (loading) return <LoadingSpinner />

  return (
    <div>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Dashboard</h1>
          <p>Overview of your tooling room assets</p>
        </div>
        <Link to="/tickets" className="btn btn-primary">
          <Ticket size={15} /> New Ticket
        </Link>
      </div>

      <div className="stat-grid">
        <StatCard icon={<Package size={22} />} label="Total Collaterals" value={stats?.total} color="#6366F1" />
        <StatCard icon={<Package size={22} />} label="Available" value={stats?.available} color="#10B981" />
        <StatCard icon={<Package size={22} />} label="Borrowed" value={stats?.borrowed} color="#F59E0B" />
        <StatCard icon={<Archive size={22} />} label="Cabinets" value={stats?.cabinets} color="#3B82F6" />
        <StatCard icon={<Boxes size={22} />} label="Products" value={stats?.products} color="#8B5CF6" />
      </div>

      <div className="card">
        <div className="card-header">
          <h3 className="card-title" style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
            <Clock size={16} /> Recent Tickets
          </h3>
          <Link to="/tickets" className="btn btn-ghost btn-sm" style={{ display: 'flex', alignItems: 'center', gap: 5 }}>
            View all <ArrowRight size={13} />
          </Link>
        </div>
        <div className="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Ticket ID</th>
                <th>Type</th>
                <th>Product</th>
                <th>Requester</th>
                <th>Status</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              {recentTickets.length === 0 ? (
                <tr><td colSpan={6} style={{ textAlign: 'center', color: 'var(--text-muted)', padding: 32 }}>No tickets yet</td></tr>
              ) : recentTickets.map(t => (
                <tr key={t.ticket_id}>
                  <td><Link to={`/tickets`} style={{ color: 'var(--accent)', fontWeight: 600 }}>{t.ticket_id}</Link></td>
                  <td style={{ textTransform: 'capitalize' }}>{t.ticket_type}</td>
                  <td>{t.product?.product_name ?? '—'}</td>
                  <td>{t.requester_name}</td>
                  <td>{statusBadge(t.status)}</td>
                  <td style={{ color: 'var(--text-muted)' }}>{t.created_at ? new Date(t.created_at).toLocaleDateString() : '—'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
