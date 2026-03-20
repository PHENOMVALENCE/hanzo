import { useNavigate } from 'react-router-dom';

export default function Landing() {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-navy text-hanzo-white flex flex-col">
      <header className="flex items-center justify-between px-8 py-4 border-b border-[rgba(201,168,76,0.15)] bg-navy">
        <div className="flex items-center gap-2">
          <span className="text-gold font-display text-xl tracking-tight">HANZO</span>
          <span className="hanzo-label text-[10px] text-hanzo-muted">
            East Africa • China
          </span>
        </div>
        <div className="flex items-center gap-3">
          <button
            type="button"
            className="hanzo-btn-secondary text-sm"
            onClick={() => navigate('/login')}
          >
            Sign in
          </button>
          <button
            type="button"
            className="hanzo-btn-primary text-sm"
            onClick={() => navigate('/login')}
          >
            Get started
          </button>
        </div>
      </header>

      <main className="flex-1 flex flex-col lg:flex-row px-8 py-12 gap-12">
        <section className="flex-1 max-w-xl">
          <div className="hanzo-label mb-3 text-gold-light">
            B2B TRADE PLATFORM
          </div>
          <h1 className="text-4xl lg:text-5xl font-display text-hanzo-white leading-tight">
            Connect East African buyers with verified Chinese factories.
          </h1>
          <p className="mt-4 text-base text-hanzo-muted max-w-lg">
            HANZO is a curated B2B trade desk for procurement teams across East Africa.
            Discover verified suppliers, request quotes in one click, and track orders end-to-end
            without exposing factory contact details or internal margins.
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <button
              type="button"
              className="hanzo-btn-primary"
              onClick={() => navigate('/login')}
            >
              Launch buyer workspace
            </button>
            <button
              type="button"
              className="hanzo-btn-secondary"
              onClick={() => navigate('/login')}
            >
              Talk to our team
            </button>
          </div>
        </section>

        <section className="flex-1">
          <div className="hanzo-card h-full p-6 flex flex-col gap-4">
            <div className="hanzo-label text-hanzo-muted">How HANZO works</div>
            <ol className="space-y-4 text-sm text-hanzo-muted">
              <li>
                <span className="text-gold mr-2">01</span>
                Browse verified product categories from trusted Chinese factories.
              </li>
              <li>
                <span className="text-gold mr-2">02</span>
                Raise RFQs with your target specs, MOQs, and timelines.
              </li>
              <li>
                <span className="text-gold mr-2">03</span>
                Compare quotes, confirm orders, and track shipments from a single dashboard.
              </li>
            </ol>
          </div>
        </section>
      </main>
    </div>
  );
}

