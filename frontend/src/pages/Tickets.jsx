import { useEffect, useState, useCallback } from 'react'
import { Plus, Eye, Trash2, Ticket as TicketIcon } from 'lucide-react'
import Modal from '../components/ui/Modal'
import Pagination from '../components/ui/Pagination'
import { statusBadge } from '../components/ui/Badge'
import { LoadingSpinner, EmptyState } from '../components/ui/Shared'
import { useAppToast } from '../components/layout/Layout'
import { getTickets, createTicket, deleteTicket, getTicket, addTicketItem, deleteTicketItem } from '../api/tickets'
import { getProducts } from '../api/products'
import { getCollaterals } from '../api/collaterals'
import { getSlots } from '../api/cabinets'

const BLANK_TICKET = { ticket_id: '', ticket_type: 'borrow', product_id: '', link_name: '', requester_name: '', issuer_name: '', status: 'pending' }

export default function Tickets() {
  const toast = useAppToast()
  const [items, setItems] = useState([])
  const [meta, setMeta] = useState(null)
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(true)
  const [filterStatus, setFilterStatus] = useState('')
  const [filterType, setFilterType] = useState('')
  const [modal, setModal] = useState(null)
  const [selected, setSelected] = useState(null)
  const [ticketDetail, setTicketDetail] = useState(null)
  const [form, setForm] = useState(BLANK_TICKET)
  const [products, setProducts] = useState([])
  const [collaterals, setCollaterals] = useState([])
  const [itemForm, setItemForm] = useState({ collateral_id: '', from_slot_id: '', condition_description: '' })
  const [saving, setSaving] = useState(false)

  const load = useCallback(async () => {
    setLoading(true)
    try {
      const params = { page, per_page: 15 }
      if (filterStatus) params.status = filterStatus
      if (filterType) params.ticket_type = filterType
      const r = await getTickets(params)
      setItems(r.data.data ?? []); setMeta(r.data.meta)
    } finally { setLoading(false) }
  }, [page, filterStatus, filterType])

  useEffect(() => { load() }, [load])
  useEffect(() => { getProducts({ per_page: 100 }).then(r => setProducts(r.data.data ?? [])) }, [])
  useEffect(() => { getCollaterals({ per_page: 100 }).then(r => setCollaterals(r.data.data ?? [])) }, [])

  const openDetail = async (id) => {
    const r = await getTicket(id)
    setTicketDetail(r.data.data)
    setItemForm({ collateral_id: '', from_slot_id: '', condition_description: '' })
    setModal('detail')
  }

  const handleCreate = async () => {
    setSaving(true)
    try {
      await createTicket(form)
      toast.success('Ticket created')
      setModal(null); load()
    } catch (e) { toast.error(Object.values(e.response?.data?.errors ?? {}).flat()[0] || 'Error') }
    finally { setSaving(false) }
  }

  const handleAddItem = async () => {
    if (!itemForm.collateral_id) return toast.warn('Select a collateral')
    setSaving(true)
    try {
      await addTicketItem(ticketDetail.ticket_id, itemForm)
      toast.success('Item added')
      const r = await getTicket(ticketDetail.ticket_id)
      setTicketDetail(r.data.data)
      setItemForm({ collateral_id: '', from_slot_id: '', condition_description: '' })
    } catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }

  const handleDeleteItem = async (itemId) => {
    try {
      await deleteTicketItem(itemId)
      toast.success('Item removed')
      const r = await getTicket(ticketDetail.ticket_id)
      setTicketDetail(r.data.data)
    } catch (e) { toast.error(e.response?.data?.message || 'Error') }
  }

  const handleDelete = async () => {
    setSaving(true)
    try { await deleteTicket(selected.ticket_id); toast.success('Deleted'); setModal(null); load() }
    catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }

  const F = ({ label, req, children }) => (
    <div className="form-group"><label className="form-label">{label}{req && <span className="req"> *</span>}</label>{children}</div>
  )

  return (
    <div>
      <div className="page-header">
        <div className="page-header-left"><h1>Tickets</h1><p>Track borrow, return and transfer workflows</p></div>
        <button className="btn btn-primary" onClick={() => { setForm(BLANK_TICKET); setModal('create') }}><Plus size={15} /> New Ticket</button>
      </div>

      <div className="filters-bar">
        <select value={filterType} onChange={e => { setFilterType(e.target.value); setPage(1) }}>
          <option value="">All Types</option>
          <option value="borrow">Borrow</option>
          <option value="return">Return</option>
          <option value="transfer">Transfer</option>
        </select>
        <select value={filterStatus} onChange={e => { setFilterStatus(e.target.value); setPage(1) }}>
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="completed">Completed</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <div className="card">
        <div className="table-wrap">
          {loading ? <LoadingSpinner /> : items.length === 0 ? (
            <EmptyState icon={<TicketIcon size={48} />} title="No tickets yet" description="Create a borrow or return ticket" />
          ) : (
            <table>
              <thead><tr><th>Ticket ID</th><th>Type</th><th>Product</th><th>Requester</th><th>Issuer</th><th>Status</th><th>Date</th><th style={{ textAlign: 'right' }}>Actions</th></tr></thead>
              <tbody>
                {items.map(t => (
                  <tr key={t.ticket_id}>
                    <td><span className="font-mono" style={{ color: 'var(--accent)' }}>{t.ticket_id}</span></td>
                    <td style={{ textTransform: 'capitalize' }}>{t.ticket_type}</td>
                    <td>{t.product?.product_name ?? '—'}</td>
                    <td>{t.requester_name}</td>
                    <td>{t.issuer_name}</td>
                    <td>{statusBadge(t.status)}</td>
                    <td style={{ color: 'var(--text-muted)', fontSize: 12.5 }}>{t.created_at ? new Date(t.created_at).toLocaleDateString() : '—'}</td>
                    <td>
                      <div style={{ display: 'flex', gap: 4, justifyContent: 'flex-end' }}>
                        <button className="btn btn-secondary btn-sm" onClick={() => openDetail(t.ticket_id)}><Eye size={13} /> View</button>
                        <button className="btn btn-ghost btn-icon btn-sm" style={{ color: 'var(--danger)' }} onClick={() => { setSelected(t); setModal('delete') }}><Trash2 size={14} /></button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
        <Pagination meta={meta} onPage={setPage} />
      </div>

      {/* Create Modal */}
      <Modal open={modal === 'create'} onClose={() => setModal(null)} title="New Ticket"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-primary" disabled={saving} onClick={handleCreate}>{saving ? 'Creating…' : 'Create'}</button></>}>
        <div className="form-row">
          <F label="Ticket ID" req><input value={form.ticket_id} onChange={e => setForm(p => ({ ...p, ticket_id: e.target.value }))} placeholder="TK-001" /></F>
          <F label="Type" req>
            <select value={form.ticket_type} onChange={e => setForm(p => ({ ...p, ticket_type: e.target.value }))}>
              <option value="borrow">Borrow</option>
              <option value="return">Return</option>
              <option value="transfer">Transfer</option>
            </select>
          </F>
        </div>
        <F label="Product">
          <select value={form.product_id} onChange={e => setForm(p => ({ ...p, product_id: e.target.value }))}>
            <option value="">No product</option>
            {products.map(p => <option key={p.product_id} value={p.product_id}>{p.product_name}</option>)}
          </select>
        </F>
        <F label="Link / Reference Name" req><input value={form.link_name} onChange={e => setForm(p => ({ ...p, link_name: e.target.value }))} placeholder="e.g. Project Alpha" /></F>
        <div className="form-row">
          <F label="Requester" req><input value={form.requester_name} onChange={e => setForm(p => ({ ...p, requester_name: e.target.value }))} placeholder="Name" /></F>
          <F label="Issuer" req><input value={form.issuer_name} onChange={e => setForm(p => ({ ...p, issuer_name: e.target.value }))} placeholder="Name" /></F>
        </div>
        <F label="Status">
          <select value={form.status} onChange={e => setForm(p => ({ ...p, status: e.target.value }))}>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="completed">Completed</option>
            <option value="rejected">Rejected</option>
          </select>
        </F>
      </Modal>

      {/* Detail Modal */}
      <Modal open={modal === 'detail'} onClose={() => setModal(null)} size="lg"
        title={ticketDetail ? `Ticket: ${ticketDetail.ticket_id}` : 'Loading…'}>
        {ticketDetail && (
          <>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '6px 24px', marginBottom: 20, fontSize: 13 }}>
              {[['Type', ticketDetail.ticket_type], ['Product', ticketDetail.product?.product_name ?? '—'], ['Requester', ticketDetail.requester_name], ['Issuer', ticketDetail.issuer_name], ['Status', ticketDetail.status], ['Date', ticketDetail.created_at ? new Date(ticketDetail.created_at).toLocaleString() : '—']].map(([k, v]) => (
                <div key={k}><span style={{ color: 'var(--text-muted)' }}>{k}:</span> <strong>{v}</strong></div>
              ))}
            </div>

            <hr className="divider" />
            <h4 style={{ marginBottom: 12, fontFamily: 'Space Grotesk, sans-serif' }}>Items ({ticketDetail.items?.length ?? 0})</h4>

            {/* Add item */}
            <div style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap' }}>
              <select style={{ flex: 2, minWidth: 160 }} value={itemForm.collateral_id} onChange={e => setItemForm(p => ({ ...p, collateral_id: e.target.value }))}>
                <option value="">Select collateral…</option>
                {collaterals.map(c => <option key={c.collateral_id} value={c.collateral_id}>{c.name}</option>)}
              </select>
              <input style={{ flex: 1, minWidth: 120 }} placeholder="Condition note" value={itemForm.condition_description} onChange={e => setItemForm(p => ({ ...p, condition_description: e.target.value }))} />
              <button className="btn btn-primary" disabled={saving} onClick={handleAddItem}><Plus size={13} /> Add</button>
            </div>

            {ticketDetail.items?.length === 0 ? (
              <p style={{ color: 'var(--text-muted)', fontSize: 13, textAlign: 'center', padding: '12px 0' }}>No items yet</p>
            ) : ticketDetail.items.map(item => (
              <div key={item.item_id} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '10px 0', borderBottom: '1px solid var(--border)' }}>
                <div>
                  <div style={{ fontWeight: 500, fontSize: 13 }}>{item.collateral?.name ?? item.collateral_id}</div>
                  <div style={{ fontSize: 12, color: 'var(--text-muted)' }}>
                    {item.from_slot_id ? `From: ${item.from_slot_id}` : ''}
                    {item.condition_description ? ` · ${item.condition_description}` : ''}
                  </div>
                </div>
                <button className="btn btn-ghost btn-sm" style={{ color: 'var(--danger)' }} onClick={() => handleDeleteItem(item.item_id)}><Trash2 size={13} /></button>
              </div>
            ))}
          </>
        )}
      </Modal>

      <Modal open={modal === 'delete'} onClose={() => setModal(null)} title="Delete Ticket" size="sm"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-danger" disabled={saving} onClick={handleDelete}>{saving ? '…' : 'Delete'}</button></>}>
        <p>Delete ticket <strong>{selected?.ticket_id}</strong>?</p>
      </Modal>
    </div>
  )
}
