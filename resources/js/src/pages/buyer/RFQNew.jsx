import { useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { apiClient } from '../../api/client';

function useQuery() {
  return new URLSearchParams(useLocation().search);
}

export default function RFQNew() {
  const query = useQuery();
  const navigate = useNavigate();
  const productId = query.get('productId');

  const [step, setStep] = useState(1);
  const [submitting, setSubmitting] = useState(false);
  const [product, setProduct] = useState(null);
  const [form, setForm] = useState({
    quantity: '',
    specs: '',
    delivery_country: '',
    delivery_city: '',
    delivery_date: '',
  });

  useEffect(() => {
    let isMounted = true;
    async function fetchProduct() {
      if (!productId) return;
      try {
        const response = await apiClient.get(`/products/${productId}`);
        if (isMounted) setProduct(response.data?.data || response.data);
      } catch {
        if (isMounted) setProduct(null);
      }
    }
    fetchProduct();
    return () => {
      isMounted = false;
    };
  }, [productId]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      await apiClient.post('/rfq', {
        product_id: productId,
        ...form,
      });
      navigate('/rfq', { replace: true });
    } catch {
      setSubmitting(false);
    }
  };

  return (
    <div className="page-enter">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="hanzo-label mb-1">RFQ</div>
          <h1 className="text-2xl font-display text-hanzo-white">New Request for Quote</h1>
          <p className="mt-2 text-sm text-hanzo-muted">
            Share your quantity, specs, and delivery details. HANZO will source pricing from
            verified factories without exposing their contact information.
          </p>
        </div>
      </div>

      <div className="hanzo-card p-4 mb-6">
        <div className="flex items-center justify-between">
          <div className="flex-1">
            <div className="hanzo-label mb-2">Progress</div>
            <div className="w-full bg-hanzo-surface h-1 rounded-full overflow-hidden">
              <div
                className="h-full bg-gold transition-all duration-150"
                style={{ width: `${(step / 3) * 100}%` }}
              />
            </div>
          </div>
          <div className="ml-4 text-xs text-hanzo-muted">
            Step {step} of 3
          </div>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {step === 1 && (
          <div className="hanzo-card p-4 space-y-4">
            <div>
              <div className="hanzo-label mb-2">Product</div>
              {product ? (
                <>
                  <div className="text-sm text-hanzo-white">{product.name}</div>
                  <div className="text-xs text-hanzo-muted mt-1">
                    {product.buyer_price_display || product.price_display} • MOQ{' '}
                    {product.moq || '-'}
                  </div>
                </>
              ) : (
                <p className="text-sm text-hanzo-muted">
                  Select a product from the catalog before raising an RFQ.
                </p>
              )}
            </div>
            <div>
              <label className="hanzo-label mb-1 block">Quantity</label>
              <input
                type="number"
                name="quantity"
                value={form.quantity}
                onChange={handleChange}
                className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
                required
              />
            </div>
          </div>
        )}

        {step === 2 && (
          <div className="hanzo-card p-4 space-y-4">
            <div>
              <label className="hanzo-label mb-1 block">Specifications</label>
              <textarea
                name="specs"
                value={form.specs}
                onChange={handleChange}
                rows={4}
                className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
                placeholder="Materials, branding, packaging, quality standards…"
                required
              />
            </div>
          </div>
        )}

        {step === 3 && (
          <div className="hanzo-card p-4 space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="hanzo-label mb-1 block">Destination country</label>
                <input
                  type="text"
                  name="delivery_country"
                  value={form.delivery_country}
                  onChange={handleChange}
                  className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
                  placeholder="Kenya, Uganda, Tanzania…"
                  required
                />
              </div>
              <div>
                <label className="hanzo-label mb-1 block">Destination city</label>
                <input
                  type="text"
                  name="delivery_city"
                  value={form.delivery_city}
                  onChange={handleChange}
                  className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
                  required
                />
              </div>
              <div>
                <label className="hanzo-label mb-1 block">Target delivery date</label>
                <input
                  type="date"
                  name="delivery_date"
                  value={form.delivery_date}
                  onChange={handleChange}
                  className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
                  required
                />
              </div>
            </div>
          </div>
        )}

        <div className="flex items-center justify-between">
          <button
            type="button"
            className="hanzo-btn-secondary"
            disabled={step === 1}
            onClick={() => setStep((s) => Math.max(1, s - 1))}
          >
            Back
          </button>
          {step < 3 ? (
            <button
              type="button"
              className="hanzo-btn-primary"
              onClick={() => setStep((s) => Math.min(3, s + 1))}
            >
              Continue
            </button>
          ) : (
            <button
              type="submit"
              className="hanzo-btn-primary"
              disabled={submitting}
            >
              {submitting ? 'Submitting…' : 'Submit RFQ'}
            </button>
          )}
        </div>
      </form>
    </div>
  );
}

