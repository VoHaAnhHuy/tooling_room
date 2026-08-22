export default function Badge({ children, type = 'default' }) {
  return <span className={`badge badge-${type}`}><span className="badge-dot" />{children}</span>
}

export function statusBadge(status) {
  if (!status) return <Badge type="default">—</Badge>
  const map = {
    available: 'success', borrowed: 'warning', maintenance: 'danger',
    pending: 'warning', approved: 'info', completed: 'success', rejected: 'danger',
    in_cabinet: 'accent', out: 'warning',
  }
  return <Badge type={map[status] || 'default'}>{status}</Badge>
}
