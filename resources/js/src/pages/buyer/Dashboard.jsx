export default function BuyerDashboard() {
  return (
    <div className="page-enter">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="hanzo-label mb-1">Buyer Portal</div>
          <h1 className="text-2xl font-display text-hanzo-white">Dashboard</h1>
          <p className="mt-2 text-sm text-hanzo-muted">
            Overview of your recent orders, RFQs, and recommended suppliers.
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div className="hanzo-card p-4">
          <div className="hanzo-label mb-2">Active RFQs</div>
          <div className="text-2xl font-display">0</div>
        </div>
        <div className="hanzo-card p-4">
          <div className="hanzo-label mb-2">Open Orders</div>
          <div className="text-2xl font-display">0</div>
        </div>
        <div className="hanzo-card p-4">
          <div className="hanzo-label mb-2">Recommended Suppliers</div>
          <div className="text-2xl font-display">0</div>
        </div>
      </div>
    </div>
  );
}

