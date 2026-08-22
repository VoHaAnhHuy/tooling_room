import { useState, useCallback } from 'react'

let toastId = 0

export function useToast() {
  const [toasts, setToasts] = useState([])

  const add = useCallback((msg, type = 'info', title = '') => {
    const id = ++toastId
    setToasts(p => [...p, { id, msg, type, title }])
    setTimeout(() => setToasts(p => p.filter(t => t.id !== id)), 3800)
  }, [])

  const success = useCallback((msg, title='Success') => add(msg, 'success', title), [add])
  const error   = useCallback((msg, title='Error')   => add(msg, 'error', title), [add])
  const warn    = useCallback((msg, title='Warning')  => add(msg, 'warning', title), [add])

  const remove  = useCallback((id) => setToasts(p => p.filter(t => t.id !== id)), [])

  return { toasts, success, error, warn, add, remove }
}
