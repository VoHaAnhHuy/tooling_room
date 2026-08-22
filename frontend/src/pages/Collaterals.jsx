import { useEffect, useState, useCallback } from 'react'
import { Plus, Search, Edit2, Trash2, MapPin, Package } from 'lucide-react'
import Modal from '../components/ui/Modal'
import Pagination from '../components/ui/Pagination'
import { statusBadge } from '../components/ui/Badge'
import { LoadingSpinner, EmptyState } from '../components/ui/Shared'
import { useAppToast } from '../components/layout/Layout'
import { getCollaterals, createCollateral, updateCollateral, deleteCollateral, assignSlot, getTypes } from '../api/collaterals'
import { getCabinets, getSlots } from '../api/cabinets'

const BLANK = { collateral_id: '', type_id: '', name: '', status: 'available', location_status: '', current_slot_id: '' }

export default function Collaterals() {
  const toast = useAppToast()
  const [items, setItems] = useState([])
  const [meta, setMeta] = useState(null)
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [filterStatus, setFilterStatus] = useState('')
  const [filterType, setFilterType] = useState('')
  const [types, setTypes] = useState([])
  const [modal, setModal] = useState(null) // null | 'create' | 'edit' | 'assign' | 'delete'
  const [selected, setSelected] = useState(null)
  const [form, setForm] = useState(BLANK)
  const [cabinets, setCabinets] = useState([])
  const [slots, setSlots] = useState([])
  const [assignForm, setAssignForm] = useState({ cabinet_id: '', slot_id: '' })
  const [saving, setSaving] = useState(false)

  const load = useCallback(async () => {
    setLoading(true)
    try {
      const params = { page, per_page: 15 }
      if (filterStatus) params.status = filterStatus
      if (filterType) params.type_id = filterType
      const res = await getCollaterals(params)
      let data = res.data.data ?? []
      if (search) data = data.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.collateral_id.toLowerCase().includes(search.toLowerCase()))
      setItems(data); setMeta(res.data.meta)
    } finally { setLoading(false) }
  }, [page, filterStatus, filterType, search])

  useEffect(() => { load() }, [load])
  useEffect(() => { getTypes({ per_page: 100 }).then(r => setTypes(r.data.data ?? [])) }, [])
  useEffect(() => { getCabinets({ per_page: 100 }).then(r => setCabinets(r.data.data ?? [])) }, [])

  const openCreate = () => { setForm(BLANK); setModal('create') }
  const openEdit   = (item) => { setSelected(item); setForm({ ...item, type_id: item.type?.type_id ?? '' }); setModal('edit') }
  const openAssign = (item) => { setSelected(item); setAssignForm({ cabinet_id: '', slot_id: '' }); setSlots([]); setModal('assign') }
  const openDelete = (item) => { setSelected(item); setModal('delete') }

  const handleCabinetChange = async (cabId) => {
    setAssignForm(p => ({ ...p, cabinet_id: cabId, slot_id: '' }))
    if (cabId) { const r = await getSlots(cabId); setSlots(r.data.data ?? []) }
    else setSlots([])
  }

  const handleSave = async () => {
    setSaving(true)
    try {
      if (modal === 'create') await createCollateral(form)
      else await updateCollateral(selected.collateral_id, form)
      toast.success(modal === 'create' ? 'Collateral created' : 'Collateral updated')
      setModal(null); load()
    } catch (e) {
      toast.error(Object.values(e.response?.data?.errors ?? {}).flat()[0] || e.response?.data?.message || 'Error')
    } finally { setSaving(false) }
  }

  const handleAssign = async () => {
    if (!assignForm.slot_id) return toast.warn('Please select a slot')
    setSaving(true)
    try {
      await assignSlot(selected.collateral_id, { slot_id: assignForm.slot_id })
      toast.success('Assigned to slot')
      setModal(null); load()
    } catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }

  const handleDelete = async () => {
    setSaving(true)
    try {
      await deleteCollateral(selected.collateral_id)
      toast.success('Deleted')
      setModal(null); load()
    } catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }

  const F = ({ label, req, children }) => (
    <div className="form-group">
      <label className="form-label">{label}{req && <span className="req"> *</span>}</label>
      {children}
    </div>
  )

  return (
    <div>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Collaterals</h1>
          <p>Manage all tools and equipment assets</p>
        </div>
        <button className="btn btn-primary" onClick={openCreate}><Plus size={15} /> Add Collateral</button>
      </div>

      <div className="filters-bar">
        <div style={{ position: 'relative', flex: 1, minWidth: 200 }}>
          <Search size={14} style={{ position: 'absolute', left: 10, top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
          <input style={{ paddingLeft: 32 }} placeholder="Search by name or ID…" value={search} onChange={e => { setSearch(e.target.value); setPage(1) }} />
        </div>
        <select value={filterStatus} onChange={e => { setFilterStatus(e.target.value); setPage(1) }}>
          <option value="">All Status</option>
          <option value="available">Available</option>
          <option value="borrowed">Borrowed</option>
          <option value="maintenance">Maintenance</option>
        </select>
        <select value={filterType} onChange={e => { setFilterType(e.target.value); setPage(1) }}>
          <option value="">All Types</option>
          {types.map(t => <option key={t.type_id} value={t.type_id}>{t.type_name}</option>)}
        </select>
      </div>

      <div className="card">
        <div className="table-wrap">
          {loading ? <LoadingSpinner /> : items.length === 0 ? (
            <EmptyState icon={<Package />} title="No collaterals found" description="Create your first collateral asset" action={<button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={13} /> Add</button>} />
          ) : (
            <table>
              <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>Location</th><th>Slot</th><th style={{ textAlign: 'right' }}>Actions</th></tr></thead>
              <tbody>
                {items.map(c => (
                  <tr key={c.collateral_id}>
                    <td><span className="font-mono">{c.collateral_id}</span></td>
                    <td style={{ fontWeight: 500 }}>{c.name}</td>
                    <td>{c.type?.type_name ?? '—'}</td>
                    <td>{statusBadge(c.status)}</td>
                    <td>{statusBadge(c.location_status)}</td>
                    <td style={{ color: 'var(--text-muted)' }}>{c.current_slot?.slot_id ?? '—'}</td>
                    <td>
                      <div style={{ display: 'flex', gap: 4, justifyContent: 'flex-end' }}>
                        <button className="btn btn-ghost btn-icon btn-sm" title="Assign slot" onClick={() => openAssign(c)}><MapPin size={14} /></button>
                        <button className="btn btn-ghost btn-icon btn-sm" onClick={() => openEdit(c)}><Edit2 size={14} /></button>
                        <button className="btn btn-ghost btn-icon btn-sm" style={{ color: 'var(--danger)' }} onClick={() => openDelete(c)}><Trash2 size={14} /></button>
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

      {/* Create / Edit Modal */}
      <Modal open={modal === 'create' || modal === 'edit'} onClose={() => setModal(null)}
        title={modal === 'create' ? 'New Collateral' : 'Edit Collateral'}
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-primary" disabled={saving} onClick={handleSave}>{saving ? 'Saving…' : 'Save'}</button></>}>
        <F label="Collateral ID" req><input value={form.collateral_id} disabled={modal === 'edit'} onChange={e => setForm(p => ({ ...p, collateral_id: e.target.value }))} placeholder="e.g. COL-001" /></F>
        <F label="Name" req><input value={form.name} onChange={e => setForm(p => ({ ...p, name: e.target.value }))} placeholder="Asset name" /></F>
        <div className="form-row">
          <F label="Type" req>
            <select value={form.type_id} onChange={e => setForm(p => ({ ...p, type_id: e.target.value }))}>
              <option value="">Select type</option>
              {types.map(t => <option key={t.type_id} value={t.type_id}>{t.type_name}</option>)}
            </select>
          </F>
          <F label="Status">
            <select value={form.status} onChange={e => setForm(p => ({ ...p, status: e.target.value }))}>
              <option value="available">Available</option>
              <option value="borrowed">Borrowed</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </F>
        </div>
      </Modal>

      {/* Assign Slot Modal */}
      <Modal open={modal === 'assign'} onClose={() => setModal(null)} title="Assign to Cabinet Slot"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-primary" disabled={saving} onClick={handleAssign}>{saving ? 'Assigning…' : 'Assign'}</button></>}>
        <p style={{ color: 'var(--text-muted)', marginBottom: 16, fontSize: 13 }}>Assigning: <strong>{selected?.name}</strong></p>
        <F label="Cabinet">
          <select value={assignForm.cabinet_id} onChange={e => handleCabinetChange(e.target.value)}>
            <option value="">Select cabinet</option>
            {cabinets.map(c => <option key={c.cabinet_id} value={c.cabinet_id}>{c.cabinet_name}</option>)}
          </select>
        </F>
        <F label="Slot">
          <select value={assignForm.slot_id} onChange={e => setAssignForm(p => ({ ...p, slot_id: e.target.value }))} disabled={!slots.length}>
            <option value="">Select slot</option>
            {slots.map(s => <option key={s.slot_id} value={s.slot_id}>{s.slot_id} (Row {s.row_index}, Col {s.column_index})</option>)}
          </select>
        </F>
      </Modal>

      {/* Delete Modal */}
      <Modal open={modal === 'delete'} onClose={() => setModal(null)} title="Delete Collateral" size="sm"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-danger" disabled={saving} onClick={handleDelete}>{saving ? 'Deleting…' : 'Delete'}</button></>}>
        <p>Are you sure you want to delete <strong>{selected?.name}</strong>? This cannot be undone.</p>
      </Modal>
    </div>
  )
}
