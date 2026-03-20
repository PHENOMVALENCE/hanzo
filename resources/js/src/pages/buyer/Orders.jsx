import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { apiClient } from '../../api/client';

export default function Orders() {
  const navigate = useNavigate();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;
    async function fetchOrders() {
      try {
        const response = await apiClient.get('/orders', {
          params: { page: 1, limit: 20 },
        });
        if (isMounted) {
          setOrders(response.data?.data || []);
        }
      } catch {
        if (isMounted) setOrders([]);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    fetchOrders();
    return () => {
      isMounted = false;
    };
  }, []);

  return (
    <div className="page-enter">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="hanzo-label mb-1">Buyer Portal</div>
          <h1 className="text-2xl font-display text-hanzo-white">Orders</h1>
          <p className="mt-2 text-sm text-hanzo-muted">
            Track confirmed shipments from Chinese factories to your East African destinations.
          </p>
        </div>
      </div>

      {loading ? (
        <p className="text-sm text-hanzo-muted">Loading orders…</p>
      ) : orders.length === 0 ? (
        <p className="text-sm text-hanzo-muted">You have no orders yet.</p>
      ) : (
        <div className="space-y-3">
          {orders.map((order) => (
            <button
              key={order.id}
              type="button"
              className="hanzo-card w-full p-4 flex items-center justify-between hover:border-gold hover:shadow-hanzo transition text-left"
              onClick={() => navigate(`/orders/${order.id}`)}
            >
              <div>
                <div className="hanzo-label mb-1">Order #{order.reference || order.id}</div>
                <div className="text-sm text-hanzo-white">
                  {order.product_name || order.title || 'Order'}
                </div>
                <div className="text-xs text-hanzo-muted mt-1">
                  {order.supplier_name || 'HANZO supplier'} •{' '}
                  {order.created_at_formatted || order.created_at}
                </div>
              </div>
              <div className="text-right">
                <div className="hanzo-label mb-1">Status</div>
                <div className="text-sm text-gold">{order.status || 'Pending'}</div>
                <div className="text-xs text-hanzo-muted mt-1">
                  {order.total_display}
                </div>
              </div>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}

