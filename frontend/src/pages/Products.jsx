import { useEffect, useState, useCallback } from 'react'
import { Plus, Edit2, Trash2, Boxes, Link2, Unlink } from 'lucide-react'
import Modal from '../components/ui/Modal'
import Pagination from '../components/ui/Pagination'
import { LoadingSpinner, EmptyState } from '../components/ui/Shared'
import { useAppToast } from '../components/layout/Layout'
import { getProducts, createProduct, updateProduct, deleteProduct, getProductCollaterals, attachCollateral, detachCollateral } from '../api/products'
import { getCollaterals } from '../api/collaterals'

export default function Products() {
  const toast = useAppToast()
  const [items, setItems] = useState([])
  const [meta, setMeta] = useState(null)
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(true)
  const [modal, setModal] = useState(null)
  const [selected, setSelected] = useState(null)
  const [form, setForm] = useState({ product_id: '', product_name: '' })
  const [productCollaterals, setProductCollaterals] = useState([])
  const [allCollaterals, setAllCollaterals] = useState([])
  const [attachId, setAttachId] = useState('')
  const [saving, setSaving] = useState(false)

  const load = useCallback(async () => {
    setLoading(true)
    try { const r = await getProducts({ page, per_page: 15 }); setItems(r.data.data ?? []); setMeta(r.data.meta) }
    finally { setLoading(false) }
  }, [page])

  useEffect(() => { load() }, [load])

  const openManage = async (item) => {
    setSelected(item)
    const [pc, ac] = await Promise.all([getProductCollaterals(item.product_id), getCollaterals({ per_page: 100 })])
    setProductCollaterals(pc.data.data ?? [])
    setAllCollaterals(ac.data.data ?? [])
    setAttachId('')
    setModal('manage')
  }

  const handleSave = async () => {
    setSaving(true)
    try {
      if (modal === 'create') await createProduct(form)
      else await updateProduct(selected.product_id, { product_name: form.product_name })
      toast.success(modal === 'create' ? 'Product created' : 'Updated')
      setModal(null); load()
    } catch (e) { toast.error(Object.values(e.response?.data?.errors ?? {}).flat()[0] || 'Error') }
    finally { setSaving(false) }
  }

  const handleAttach = async () => {
    if (!attachId) return toast.warn('Select a collateral')
    setSaving(true)
    try {
      await attachCollateral(selected.product_id, { collateral_id: attachId })
      toast.success('Collateral linked')
      const r = await getProductCollaterals(selected.product_id)
      setProductCollaterals(r.data.data ?? [])
      setAttachId('')
    } catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }

  const handleDetach = async (collateralId) => {
    try {
      await detachCollateral(selected.product_id, collateralId)
      toast.success('Unlinked')
      setProductCollaterals(p => p.filter(c => c.collateral_id !== collateralId))
    } catch (e) { toast.error(e.response?.data?.message || 'Error') }
  }

  const handleDelete = async () => {
    setSaving(true)
    try { await deleteProduct(selected.product_id); toast.success('Deleted'); setModal(null); load() }
    catch (e) { toast.error(e.response?.data?.message || 'Error') }
    finally { setSaving(false) }
  }

  const F = ({ label, req, children }) => (
    <div className="form-group"><label className="form-label">{label}{req && <span className="req"> *</span>}</label>{children}</div>
  )

  const availableToAttach = allCollaterals.filter(c => !productCollaterals.find(pc => pc.collateral_id === c.collateral_id))

  return (
    <div>
      <div className="page-header">
        <div className="page-header-left"><h1>Products</h1><p>Manage products and their associated collaterals</p></div>
        <button className="btn btn-primary" onClick={() => { setForm({ product_id: '', product_name: '' }); setModal('create') }}><Plus size={15} /> Add Product</button>
      </div>

      <div className="card">
        <div className="table-wrap">
          {loading ? <LoadingSpinner /> : items.length === 0 ? (
            <EmptyState icon={<Boxes size={48} />} title="No products yet" />
          ) : (
            <table>
              <thead><tr><th>Product ID</th><th>Name</th><th style={{ textAlign: 'right' }}>Actions</th></tr></thead>
              <tbody>
                {items.map(p => (
                  <tr key={p.product_id}>
                    <td><span className="font-mono">{p.product_id}</span></td>
                    <td style={{ fontWeight: 500 }}>{p.product_name}</td>
                    <td>
                      <div style={{ display: 'flex', gap: 4, justifyContent: 'flex-end' }}>
                        <button className="btn btn-secondary btn-sm" onClick={() => openManage(p)}><Link2 size={13} /> Collaterals</button>
                        <button className="btn btn-ghost btn-icon btn-sm" onClick={() => { setSelected(p); setForm({ product_id: p.product_id, product_name: p.product_name }); setModal('edit') }}><Edit2 size={14} /></button>
                        <button className="btn btn-ghost btn-icon btn-sm" style={{ color: 'var(--danger)' }} onClick={() => { setSelected(p); setModal('delete') }}><Trash2 size={14} /></button>
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

      <Modal open={modal === 'create' || modal === 'edit'} onClose={() => setModal(null)}
        title={modal === 'create' ? 'New Product' : 'Edit Product'} size="sm"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-primary" disabled={saving} onClick={handleSave}>{saving ? 'Saving…' : 'Save'}</button></>}>
        <F label="Product ID" req><input value={form.product_id} disabled={modal === 'edit'} onChange={e => setForm(p => ({ ...p, product_id: e.target.value }))} placeholder="e.g. PRD-001" /></F>
        <F label="Product Name" req><input value={form.product_name} onChange={e => setForm(p => ({ ...p, product_name: e.target.value }))} placeholder="Product name" /></F>
      </Modal>

      <Modal open={modal === 'manage'} onClose={() => setModal(null)} title={`Collaterals — ${selected?.product_name}`} size="lg">
        <div style={{ marginBottom: 18 }}>
          <div style={{ display: 'flex', gap: 10 }}>
            <select style={{ flex: 1 }} value={attachId} onChange={e => setAttachId(e.target.value)}>
              <option value="">Select collateral to link…</option>
              {availableToAttach.map(c => <option key={c.collateral_id} value={c.collateral_id}>{c.name} ({c.collateral_id})</option>)}
            </select>
            <button className="btn btn-primary" disabled={saving} onClick={handleAttach}><Link2 size={14} /> Link</button>
          </div>
        </div>
        <hr className="divider" />
        {productCollaterals.length === 0 ? (
          <p style={{ textAlign: 'center', color: 'var(--text-muted)', fontSize: 13, padding: '16px 0' }}>No collaterals linked yet</p>
        ) : productCollaterals.map(c => (
          <div key={c.collateral_id} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '10px 0', borderBottom: '1px solid var(--border)' }}>
            <div>
              <div style={{ fontWeight: 500 }}>{c.name}</div>
              <div style={{ fontSize: 12, color: 'var(--text-muted)' }}>{c.collateral_id}</div>
            </div>
            <button className="btn btn-ghost btn-sm" style={{ color: 'var(--danger)' }} onClick={() => handleDetach(c.collateral_id)}><Unlink size={13} /> Unlink</button>
          </div>
        ))}
      </Modal>

      <Modal open={modal === 'delete'} onClose={() => setModal(null)} title="Delete Product" size="sm"
        footer={<><button className="btn btn-secondary" onClick={() => setModal(null)}>Cancel</button><button className="btn btn-danger" disabled={saving} onClick={handleDelete}>{saving ? '…' : 'Delete'}</button></>}>
        <p>Delete <strong>{selected?.product_name}</strong>?</p>
      </Modal>
    </div>
  )
}
