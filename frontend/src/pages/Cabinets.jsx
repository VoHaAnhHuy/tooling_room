import { useEffect, useState, useCallback } from 'react'
import { Plus, Edit2, Trash2, Archive, ChevronDown, ChevronUp } from 'lucide-react'
import Modal from '../components/ui/Modal'
import { LoadingSpinner, EmptyState } from '../components/ui/Shared'
import { useAppToast } from '../components/layout/Layout'
import { getCabinets, createCabinet, updateCabinet, deleteCabinet, getSlots, createSlot, deleteSlot } from '../api/cabinets'

const F = ({ label, req, children }) => (
  <div className="form-group"><label className="form-label">{label}{req && <span className="req"> *</span>}</label>{children}</div>
)

export default function Cabinets() {
  const toast = useAppToast()
  const [cabinets, setCabinets] = useState([])
  const [loading, setLoading] = useState(true)
  const [expanded, setExpanded] = useState({})
  const [slots, setSlots] = useState({})
  const [modal, setModal] = useState(null)
  const [selected, setSelected] = useState(null)
  const [form, setForm] = useState({ cabinet_id: '', cabinet_name: '', total_rows: 4, total_columns: 4 })
  const [saving, setSaving] = useState(false)

  const load = useCallback(async () => {
    setLoading(true)
    try { const r = await getCabinets({ per_page: 100 }); setCabinets(r.data.data ?? []) }
    finally { setLoading(false) }
  }, [])

  useEffect(() => { load() }, [load])

  const toggleExpand = async (cabId) => {
    const isOpen = expanded[cabId]
    setExpanded(p => ({ ...p, [cabId]: !isOpen }))
    if (!isOpen && !slots[cabId]) {
      const r = await getSlots(cabId)
      setSlots(p => ({ ...p, [cabId]: r.data.data ?? [] }))
    }
  }

  const handleSave = async () => {
    setSaving(true)
    try {
      if (modal === 'create') await createCabinet(form)
      else await updateCabinet(selected.cabinet_id, { cabinet_name: form.cabinet_name, total_rows: form.total_rows, total_columns: form.total_columns })
      toast.success(modal === 'create' ? 'Cabinet created' : 'Cabinet updated')
      setModal(null); load()
    } catch (e) { toast.error(Object.values(e.response?.data?.errors ?? {}).flat()[0] || 'Error') }
    finally { setSaving(false) }
  }

  const handleDelete = async () => {
    setSaving(true)
    try { await deleteCabinet(selected.cabinet_id); toast.success('Cabinet deleted'); setModal(null); load() }
    catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }
  if (loading) return <LoadingSpinner />

  return (
    <div>
      <div className="page-header">
        <div className="page-header-left"><h1>Cabinets</h1><p>Manage storage cabinets and their slots</p></div>
        <button className="btn btn-primary" onClick={() => { setForm({ cabinet_id: '', cabinet_name: '', total_rows: 4, total_columns: 4 }); setModal('create') }}><Plus size={15} /> Add Cabinet</button>
      </div>

      {cabinets.length === 0 ? (
        <EmptyState icon={<Archive size={48} />} title="No cabinets yet" description="Add your first storage cabinet" />
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
          {cabinets.map(cab => {
            const isOpen = expanded[cab.cabinet_id]
            const cabSlots = slots[cab.cabinet_id] ?? []

            // Build slot grid
            const grid = Array.from({ length: cab.total_rows }, (_, r) =>
              Array.from({ length: cab.total_columns }, (_, c) => {
                const slot = cabSlots.find(s => s.row_index === r && s.column_index === c)
                return { r, c, slot }
              })
            )

            return (
              <div className="card" key={cab.cabinet_id}>
                <div className="card-header" style={{ cursor: 'pointer' }} onClick={() => toggleExpand(cab.cabinet_id)}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{ width: 36, height: 36, background: 'var(--accent-light)', borderRadius: 8, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                      <Archive size={18} color="var(--accent)" />
                    </div>
                    <div>
                      <div className="card-title">{cab.cabinet_name}</div>
                      <div style={{ fontSize: 12, color: 'var(--text-muted)' }}>{cab.cabinet_id} · {cab.total_rows}×{cab.total_columns} grid · {cabSlots.length} slots</div>
                    </div>
                  </div>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                    <button className="btn btn-ghost btn-icon btn-sm" onClick={e => { e.stopPropagation(); setSelected(cab); setForm({ cabinet_id: cab.cabinet_id, cabinet_name: cab.cabinet_name, total_rows: cab.total_rows, total_columns: cab.total_columns }); setModal('edit') }}><Edit2 size={14} /></button>
                    <button className="btn btn-ghost btn-icon btn-sm" style={{ color: 'var(--danger)' }} onClick={e => { e.stopPropagation(); setSelected(cab); setModal('delete') }}><Trash2 size={14} /></button>
                    {isOpen ? <ChevronUp size={16} color="var(--text-muted)" /> : <ChevronDown size={16} color="var(--text-muted)" />}
                  </div>
                </div>

                {isOpen && (
                  <div className="card-body">
                    {cabSlots.length === 0 ? (
                      <p style={{ color: 'var(--text-muted)', fontSize: 13, textAlign: 'center', padding: '20px 0' }}>No slots yet — click "+ Slot" to add</p>
                    ) : (
                      <div className="slot-grid" style={{ gridTemplateColumns: `repeat(${cab.total_columns}, 1fr)` }}>
                        {grid.flat().map(({ r, c, slot }) => (
                          <div key={`${r}-${c}`} className={`slot-cell ${slot ? (slot.current_collateral ? 'occupied' : '') : 'empty'}`} title={slot ? slot.slot_id : 'Empty'}>
                            {slot ? (
                              <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 2, width: '100%' }}>
                                <span style={{ fontSize: 10, fontWeight: 600 }}>{slot.slot_id}</span>
                                {slot.current_collateral && <span style={{ fontSize: 9, opacity: .8, maxWidth: '100%', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{slot.current_collateral.name}</span>}
                              </div>
                            ) : <span style={{ fontSize: 10 }}>({r},{c})</span>}
                          </div>
                        ))}
                      </div>
                    )}
                  </div>
                )}
              </div>
            )
          })}
        </div>
      )}

      <Modal open={modal === 'create' || modal === 'edit'} onClose={() => setModal(null)}
        title={modal === 'create' ? 'New Cabinet' : 'Edit Cabinet'}
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-primary" disabled={saving} onClick={handleSave}>{saving ? 'Saving…' : 'Save'}</button></>}>
        <F label="Cabinet ID" req><input value={form.cabinet_id} disabled={modal === 'edit'} onChange={e => setForm(p => ({ ...p, cabinet_id: e.target.value }))} placeholder="e.g. CAB-01" /></F>
        <F label="Cabinet Name" req><input value={form.cabinet_name} onChange={e => setForm(p => ({ ...p, cabinet_name: e.target.value }))} placeholder="e.g. Cabinet A" /></F>
        <div className="form-row">
          <F label="Rows"><input type="number" min={1} value={form.total_rows} onChange={e => setForm(p => ({ ...p, total_rows: +e.target.value }))} /></F>
          <F label="Columns"><input type="number" min={1} value={form.total_columns} onChange={e => setForm(p => ({ ...p, total_columns: +e.target.value }))} /></F>
        </div>
      </Modal>

      <Modal open={modal === 'delete'} onClose={() => setModal(null)} title="Delete Cabinet" size="sm"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-danger" disabled={saving} onClick={handleDelete}>{saving ? '…' : 'Delete'}</button></>}>
        <p>Delete <strong>{selected?.cabinet_name}</strong>? This action cannot be undone.</p>
      </Modal>
    </div>
  )
}
