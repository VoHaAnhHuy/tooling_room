import { createContext, useContext, useState, useCallback } from 'react'
import { login as apiLogin, logout as apiLogout } from '../api/auth'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser]     = useState(() => {
    try { return JSON.parse(localStorage.getItem('user')) } catch { return null }
  })
  const [token, setToken]   = useState(() => localStorage.getItem('token'))

  const login = useCallback(async (email, password) => {
    const res = await apiLogin({ email, password })
    const { user: u, token: t } = res.data.data
    localStorage.setItem('token', t)
    localStorage.setItem('user', JSON.stringify(u))
    setUser(u); setToken(t)
    return u
  }, [])

  const logout = useCallback(async () => {
    try { await apiLogout() } catch {}
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    setUser(null); setToken(null)
  }, [])

  return (
    <AuthContext.Provider value={{ user, token, login, logout, isAuth: !!token }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)
