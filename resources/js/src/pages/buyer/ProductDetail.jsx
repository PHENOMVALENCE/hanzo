import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { apiClient } from '../../api/client';

export default function ProductDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;
    async function fetchProduct() {
      try {
        const response = await apiClient.get(`/products/${id}`);
        if (isMounted) {
          setProduct(response.data?.data || response.data);
        }
      } catch {
        if (isMounted) setProduct(null);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    fetchProduct();
    return () => {
      isMounted = false;
    };
  }, [id]);

  if (loading) {
    return <p className="text-sm text-hanzo-muted">Loading product…</p>;
  }

  if (!product) {
    return <p className="text-sm text-hanzo-muted">Product not found.</p>;
  }

  return (
    <div className="page-enter">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="hanzo-label mb-1">Product</div>
          <h1 className="text-2xl font-display text-hanzo-white">
            {product.name}
          </h1>
          <p className="mt-2 text-sm text-hanzo-muted max-w-xl">
            {product.description}
          </p>
        </div>
        <button
          type="button"
          className="hanzo-btn-primary"
          onClick={() => navigate(`/rfq/new?productId=${product.id}`)}
        >
          Request RFQ
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="hanzo-card p-4 md:col-span-2">
          <div className="hanzo-label mb-2">Buyer price</div>
          <div className="text-3xl font-display text-gold">
            {product.buyer_price_display || product.price_display}
          </div>
          <p className="mt-4 text-sm text-hanzo-muted">
            Pricing shown here is buyer-facing only. Factory costs and margin structures
            are managed internally by HANZO and are never exposed.
          </p>
        </div>

        <div className="hanzo-card p-4">
          <div className="hanzo-label mb-2">Key details</div>
          <p className="text-sm text-hanzo-muted">
            MOQ: <span className="text-hanzo-white">{product.moq || '-'}</span>
          </p>
          <p className="text-sm text-hanzo-muted mt-1">
            Category:{' '}
            <span className="text-hanzo-white">{product.category || '-'}</span>
          </p>
        </div>
      </div>
    </div>
  );
}

