import Sidebar from './Sidebar'
import ToastContainer from '../ui/Toast'
import { useToast } from '../../hooks/useToast'
import { createContext, useContext } from 'react'

const ToastCtx = createContext(null)
export const useAppToast = () => useContext(ToastCtx)

export default function Layout({ children }) {
  const toast = useToast()
  return (
    <ToastCtx.Provider value={toast}>
      <div className="layout">
        <Sidebar />
        <div className="main-wrap">
          <main className="page-content">{children}</main>
        </div>
        <ToastContainer toasts={toast.toasts} remove={toast.remove} />
      </div>
    </ToastCtx.Provider>
  )
}
