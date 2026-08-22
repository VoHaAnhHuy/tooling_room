import api from './axios'
export const getTickets    = (params)    => api.get('/tickets', { params })
export const getTicket     = (id)        => api.get(`/tickets/${id}`)
export const createTicket  = (data)      => api.post('/tickets', data)
export const updateTicket  = (id,data)   => api.put(`/tickets/${id}`, data)
export const deleteTicket  = (id)        => api.delete(`/tickets/${id}`)
export const getTicketItems= (ticketId)  => api.get(`/tickets/${ticketId}/items`)
export const addTicketItem = (ticketId,d)=> api.post(`/tickets/${ticketId}/items`, d)
export const deleteTicketItem = (itemId) => api.delete(`/ticket-items/${itemId}`)
