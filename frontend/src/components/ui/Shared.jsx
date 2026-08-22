export function LoadingSpinner() {
  return <div className="loading-center"><div className="spinner" /></div>
}

export function EmptyState({ icon, title, description, action }) {
  return (
    <div className="empty-state">
      {icon}
      <h3>{title}</h3>
      {description && <p>{description}</p>}
      {action}
    </div>
  )
}

export function StatCard({ icon, label, value, color = '#6366F1', change }) {
  return (
    <div className="stat-card">
      <div className="stat-icon" style={{ background: color + '18' }}>
        <span style={{ color }}>{icon}</span>
      </div>
      <div>
        <div className="stat-value">{value ?? '—'}</div>
        <div className="stat-label">{label}</div>
        {change && <div className="stat-change">{change}</div>}
      </div>
    </div>
  )
}
