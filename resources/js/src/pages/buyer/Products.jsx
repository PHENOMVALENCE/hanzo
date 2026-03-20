import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { apiClient } from '../../api/client';

export default function Products() {
  const navigate = useNavigate();
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;
    async function fetchProducts() {
      try {
        const response = await apiClient.get('/products', {
          params: { page: 1, limit: 12 },
        });
        if (isMounted) {
          setProducts(response.data?.data || []);
        }
      } catch {
        if (isMounted) {
          setProducts([]);
        }
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    fetchProducts();
    return () => {
      isMounted = false;
    };
  }, []);

  return (
    <div className="page-enter">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="hanzo-label mb-1">Buyer Portal</div>
          <h1 className="text-2xl font-display text-hanzo-white">Products</h1>
          <p className="mt-2 text-sm text-hanzo-muted">
            Browse buyer-ready pricing from verified suppliers. Internal margins are never shown.
          </p>
        </div>
      </div>

      {loading ? (
        <p className="text-sm text-hanzo-muted">Loading products…</p>
      ) : products.length === 0 ? (
        <p className="text-sm text-hanzo-muted">No products yet.</p>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {products.map((product) => (
            <button
              key={product.id}
              type="button"
              className="hanzo-card p-4 text-left hover:border-gold hover:shadow-hanzo transition"
              onClick={() => navigate(`/products/${product.id}`)}
            >
              <div className="hanzo-label mb-2 text-gold-light">
                {product.category || 'General'}
              </div>
              <h2 className="text-lg font-display text-hanzo-white">
                {product.name}
              </h2>
              <p className="mt-2 text-sm text-hanzo-muted line-clamp-2">
                {product.description}
              </p>
              <div className="mt-4 flex items-center justify-between">
                <div>
                  <div className="hanzo-label mb-1">Buyer price</div>
                  <div className="text-base font-display text-gold">
                    {product.buyer_price_display || product.price_display}
                  </div>
                </div>
                <div className="text-right">
                  <div className="hanzo-label mb-1">MOQ</div>
                  <div className="text-sm text-hanzo-white">
                    {product.moq || '-'}
                  </div>
                </div>
              </div>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}

