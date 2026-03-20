import { create } from 'zustand';

const STORAGE_KEY = 'hanzo_token';
const ROLE_KEY = 'hanzo_role';

const getInitialAuthState = () => {
  if (typeof window === 'undefined') {
    return { token: null, role: null, user: null };
  }

  const token = window.localStorage.getItem(STORAGE_KEY);
  const role = window.localStorage.getItem(ROLE_KEY);

  return {
    token: token || null,
    role: role || null,
    user: null,
  };
};

export const useAuthStore = create((set) => ({
  ...getInitialAuthState(),
  login: ({ token, role, user }) => {
    window.localStorage.setItem(STORAGE_KEY, token);
    window.localStorage.setItem(ROLE_KEY, role);
    set({ token, role, user: user || null });
  },
  logout: () => {
    window.localStorage.removeItem(STORAGE_KEY);
    window.localStorage.removeItem(ROLE_KEY);
    set({ token: null, role: null, user: null });
  },
  setUser: (user) => set({ user }),
}));

