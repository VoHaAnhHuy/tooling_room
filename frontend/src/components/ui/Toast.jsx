import { CheckCircle, AlertCircle, AlertTriangle, Info, X } from 'lucide-react'

const icons = {
  success: <CheckCircle size={18} color="var(--success)" />,
  error:   <AlertCircle size={18} color="var(--danger)" />,
  warning: <AlertTriangle size={18} color="var(--warning)" />,
  info:    <Info size={18} color="var(--info)" />,
}

export default function ToastContainer({ toasts, remove }) {
  return (
    <div className="toast-container">
      {toasts.map(t => (
        <div key={t.id} className={`toast toast-${t.type}`}>
          {icons[t.type]}
          <div className="toast-msg">
            {t.title && <div className="toast-title">{t.title}</div>}
            <div style={{ fontSize: 13 }}>{t.msg}</div>
          </div>
          <button className="toast-close" onClick={() => remove(t.id)}><X size={14} /></button>
        </div>
      ))}
    </div>
  )
}
