import { ChevronLeft, ChevronRight } from 'lucide-react'

export default function Pagination({ meta, onPage }) {
  if (!meta || meta.last_page <= 1) return null
  const pages = Array.from({ length: meta.last_page }, (_, i) => i + 1)
  return (
    <div className="pagination">
      <button className="page-btn" disabled={meta.current_page === 1}
        onClick={() => onPage(meta.current_page - 1)}>
        <ChevronLeft size={14} />
      </button>
      {pages.map(p => (
        <button key={p} className={`page-btn ${p === meta.current_page ? 'active' : ''}`}
          onClick={() => onPage(p)}>{p}</button>
      ))}
      <button className="page-btn" disabled={meta.current_page === meta.last_page}
        onClick={() => onPage(meta.current_page + 1)}>
        <ChevronRight size={14} />
      </button>
      <span className="page-info">{meta.total} total</span>
    </div>
  )
}
