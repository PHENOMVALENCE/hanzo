import { useState } from 'react';
import { Navigate, Outlet, NavLink } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';

const NAV_ITEMS = {
  buyer: [
    { path: '/dashboard', label: 'Dashboard' },
    { path: '/suppliers', label: 'Suppliers' },
    { path: '/products', label: 'Products' },
    { path: '/rfq', label: 'RFQs' },
    { path: '/orders', label: 'Orders' },
    { path: '/messages', label: 'Messages' },
    { path: '/profile', label: 'Profile' },
  ],
  admin: [
    { path: '/admin/dashboard', label: 'Dashboard' },
    { path: '/admin/suppliers', label: 'Suppliers' },
    { path: '/admin/buyers', label: 'Buyers' },
    { path: '/admin/orders', label: 'Orders' },
    { path: '/admin/rfqs', label: 'RFQs' },
    { path: '/admin/margins', label: 'Margins' },
    { path: '/admin/messages', label: 'Messages' },
  ],
};

function Sidebar({ role, collapsed }) {
  const items = NAV_ITEMS[role] || [];
  return (
    <aside
      className={`h-screen bg-navy-mid border-r border-[rgba(201,168,76,0.15)] transition-all duration-200 ease-out ${
        collapsed ? 'w-16' : 'w-60'
      }`}
    >
      <div className="flex items-center h-16 px-4 border-b border-[rgba(201,168,76,0.15)]">
        <span className="text-gold font-display font-semibold tracking-wide text-sm">
          {collapsed ? 'HZ' : 'HANZO'}
        </span>
      </div>
      <nav className="mt-4 space-y-1 px-2">
        {items.map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            className={({ isActive }) =>
              `flex items-center px-3 py-2 rounded-md text-sm transition-colors duration-150 border-l-2 ${
                isActive
                  ? 'text-gold bg-[rgba(201,168,76,0.1)] border-gold'
                  : 'text-hanzo-muted hover:text-gold hover:bg-[rgba(201,168,76,0.06)] border-transparent'
              }`
            }
          >
            <span className={collapsed ? 'sr-only' : 'truncate'}>{item.label}</span>
          </NavLink>
        ))}
      </nav>
    </aside>
  );
}

function Topbar() {
  const { user, logout } = useAuthStore();

  return (
    <header className="h-16 border-b border-[rgba(201,168,76,0.15)] flex items-center justify-between px-6 bg-navy">
      <div className="flex-1 max-w-md">
        <input
          className="w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-1.5 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
          placeholder="Search suppliers, products, RFQs..."
        />
      </div>
      <div className="flex items-center gap-4">
        <button
          type="button"
          className="relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-[rgba(201,168,76,0.2)] text-hanzo-muted hover:text-gold hover:border-gold transition-colors duration-150"
        >
          <span className="hanzo-label text-[10px]">Bell</span>
        </button>
        <div className="flex items-center gap-2">
          <div className="text-right">
            <div className="text-sm font-medium text-hanzo-white">
              {user?.name || 'User'}
            </div>
            <div className="text-xs text-hanzo-muted uppercase tracking-[0.08em]">
              {user?.role || 'Buyer'}
            </div>
          </div>
          <button
            type="button"
            onClick={logout}
            className="hanzo-btn-secondary text-xs px-2 py-1"
          >
            Logout
          </button>
        </div>
      </div>
    </header>
  );
}

export function AppShell() {
  const { token, role } = useAuthStore();
  const [collapsed] = useState(false);

  const isAuthenticated = Boolean(token && role);

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return (
    <div className="flex min-h-screen bg-navy text-hanzo-white">
      <Sidebar role={role} collapsed={collapsed} />
      <div className="flex-1 flex flex-col">
        <Topbar />
        <main className="flex-1 overflow-y-auto bg-navy px-6 py-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
}

