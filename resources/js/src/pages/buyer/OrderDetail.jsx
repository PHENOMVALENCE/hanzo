import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { apiClient } from '../../api/client';

export default function OrderDetail() {
  const { id } = useParams();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;
    async function fetchOrder() {
      try {
        const response = await apiClient.get(`/orders/${id}`);
        if (isMounted) {
          setOrder(response.data?.data || response.data);
        }
      } catch {
        if (isMounted) setOrder(null);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    fetchOrder();
    return () => {
      isMounted = false;
    };
  }, [id]);

  if (loading) {
    return <p className="text-sm text-hanzo-muted">Loading order…</p>;
  }

  if (!order) {
    return <p className="text-sm text-hanzo-muted">Order not found.</p>;
  }

  const steps = ['Confirmed', 'Processing', 'Shipped', 'Delivered'];

  return (
    <div className="page-enter">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="hanzo-label mb-1">Order</div>
          <h1 className="text-2xl font-display text-hanzo-white">
            Order #{order.reference || order.id}
          </h1>
          <p className="mt-2 text-sm text-hanzo-muted">
            {order.product_name || 'HANZO order'} •{' '}
            {order.created_at_formatted || order.created_at}
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="hanzo-card p-4 md:col-span-2">
          <div className="hanzo-label mb-2">Order value</div>
          <div className="text-2xl font-display text-gold">
            {order.total_display}
          </div>
          <p className="mt-4 text-sm text-hanzo-muted">
            All supplier terms, factory contact details, and internal costs are handled
            via HANZO operations and are not displayed in this buyer view.
          </p>
        </div>

        <div className="hanzo-card p-4">
          <div className="hanzo-label mb-2">Shipment status</div>
          <ol className="space-y-2 text-sm text-hanzo-muted">
            {steps.map((step) => {
              const isCurrent = order.status === step;
              const isCompleted = steps.indexOf(order.status) > steps.indexOf(step);
              return (
                <li
                  key={step}
                  className={`flex items-center gap-2 ${
                    isCurrent ? 'text-gold' : isCompleted ? 'text-hanzo-white' : ''
                  }`}
                >
                  <span
                    className={`w-2 h-2 rounded-full ${
                      isCompleted || isCurrent ? 'bg-gold' : 'bg-hanzo-muted'
                    }`}
                  />
                  <span>{step}</span>
                </li>
              );
            })}
          </ol>
        </div>
      </div>
    </div>
  );
}

