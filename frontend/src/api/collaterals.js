import api from './axios'
export const getCollaterals  = (params) => api.get('/collaterals', { params })
export const getCollateral   = (id)     => api.get(`/collaterals/${id}`)
export const createCollateral= (data)   => api.post('/collaterals', data)
export const updateCollateral= (id,data)=> api.put(`/collaterals/${id}`, data)
export const deleteCollateral= (id)     => api.delete(`/collaterals/${id}`)
export const assignSlot      = (id,data)=> api.post(`/collaterals/${id}/assign-slot`, data)
export const getTypes        = (params) => api.get('/collateral-types', { params })
export const createType      = (data)   => api.post('/collateral-types', data)
