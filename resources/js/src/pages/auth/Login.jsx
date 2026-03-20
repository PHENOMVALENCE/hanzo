import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { apiClient } from '../../api/client';
import { useAuthStore } from '../../store/authStore';

export default function Login() {
  const navigate = useNavigate();
  const login = useAuthStore((s) => s.login);

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState('buyer');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const response = await apiClient.post('/auth/login', {
        email,
        password,
        role,
      });

      const { token, user } = response.data || {};
      login({ token, role: user?.role || role, user });

      if ((user?.role || role) === 'admin') {
        navigate('/admin/dashboard', { replace: true });
      } else {
        navigate('/dashboard', { replace: true });
      }
    } catch (err) {
      const message =
        err?.response?.data?.message || 'Unable to sign in. Please check your credentials.';
      setError(message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-navy">
      <div className="hanzo-card w-full max-w-md p-8">
        <div className="mb-6 text-center">
          <div className="hanzo-label mb-1">Welcome to</div>
          <h1 className="text-2xl font-display text-gold tracking-tight">HANZO Trade Desk</h1>
          <p className="mt-2 text-sm text-hanzo-muted">
            Sign in to access your B2B trade workspace.
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="hanzo-label mb-1 block">Email</label>
            <input
              type="email"
              className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
              placeholder="you@company.co.ke"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>

          <div>
            <label className="hanzo-label mb-1 block">Password</label>
            <input
              type="password"
              className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white placeholder:text-hanzo-muted focus:outline-none focus:ring-1 focus:ring-gold"
              placeholder="Enter your password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          <div>
            <label className="hanzo-label mb-1 block">Sign in as</label>
            <select
              className="mt-1 block w-full bg-hanzo-surface border border-[rgba(201,168,76,0.15)] rounded-md px-3 py-2 text-sm text-hanzo-white focus:outline-none focus:ring-1 focus:ring-gold"
              value={role}
              onChange={(e) => setRole(e.target.value)}
            >
              <option value="buyer">Buyer</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          {error && (
            <p className="text-sm text-hanzo-error bg-[rgba(224,92,92,0.1)] border border-[rgba(224,92,92,0.4)] rounded-md px-3 py-2">
              {error}
            </p>
          )}

          <button
            type="submit"
            className="hanzo-btn-primary w-full mt-2"
            disabled={loading}
          >
            {loading ? 'Signing in...' : 'Sign In'}
          </button>
        </form>
      </div>
    </div>
  );
}

