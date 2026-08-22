import api from './axios'
export const getCabinets  = (params) => api.get('/cabinets', { params })
export const getCabinet   = (id)     => api.get(`/cabinets/${id}`)
export const createCabinet= (data)   => api.post('/cabinets', data)
export const updateCabinet= (id,data)=> api.put(`/cabinets/${id}`, data)
export const deleteCabinet= (id)     => api.delete(`/cabinets/${id}`)
export const getSlots     = (cabId)  => api.get(`/cabinets/${cabId}/slots`)
export const createSlot   = (cabId,data)=> api.post(`/cabinets/${cabId}/slots`, data)
export const deleteSlot   = (slotId) => api.delete(`/slots/${slotId}`)
