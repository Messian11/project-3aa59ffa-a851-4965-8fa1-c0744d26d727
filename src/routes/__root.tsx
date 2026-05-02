import { Outlet, Link, createRootRoute, HeadContent, Scripts } from "@tanstack/react-router";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import {
  Search,
  ArrowRight,
  Home,
  LayoutGrid,
  MapPin,
  BookOpen,
  Sparkles,
  Compass,
} from "lucide-react";

import appCss from "../styles.css?url";

const popularLinks = [
  { to: "/", label: "Главная", icon: Home, tone: "from-brand-blue/15 to-brand-green/10 text-brand-blue" },
  { to: "/mfo", label: "Каталог МФО", icon: LayoutGrid, tone: "from-brand-green/15 to-brand-blue/10 text-brand-green" },
  { to: "/blog", label: "Блог и гиды", icon: BookOpen, tone: "from-brand-amber/20 to-brand-green/10 text-brand-amber" },
] as const;

const quickJumps = [
  { to: "/summa/$slug", params: { slug: "zaim-10000" }, label: "Займ 10 000 ₽" },
  { to: "/summa/$slug", params: { slug: "zaim-30000" }, label: "Займ 30 000 ₽" },
  { to: "/situations/$slug", params: { slug: "srochno" }, label: "Срочно за 5 минут" },
  { to: "/goroda/$slug", params: { slug: "moskva" }, label: "Займы в Москве" },
] as const;

function NotFoundComponent() {
  return (
    <div className="min-h-screen bg-white">
      <SiteHeader />

      <section className="relative overflow-hidden px-5 py-16 md:px-6 md:py-24">
        {/* Decorative blobs */}
        <div className="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-brand-blue/15 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-32 -left-24 h-80 w-80 rounded-full bg-brand-green/15 blur-3xl" />
        <div className="pointer-events-none absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-brand-amber/10 blur-3xl" />

        <div className="relative mx-auto max-w-3xl text-center">
          <span className="inline-flex items-center gap-1.5 rounded-pill bg-brand-amber/15 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-brand-amber ring-1 ring-inset ring-brand-amber/30">
            <Sparkles className="h-3 w-3" /> Ошибка 404
          </span>

          {/* Big 404 with gradient */}
          <h1
            className="mt-6 select-none text-[120px] font-black leading-none tracking-tight sm:text-[160px] md:text-[200px]"
            style={{
              background: "linear-gradient(135deg, #10b981 0%, #2563eb 50%, #f59e0b 100%)",
              WebkitBackgroundClip: "text",
              WebkitTextFillColor: "transparent",
              backgroundClip: "text",
            }}
          >
            404
          </h1>

          <h2 className="mt-2 text-2xl font-extrabold tracking-tight text-brand-ink sm:text-3xl md:text-4xl">
            Страница не найдена
          </h2>
          <p className="mx-auto mt-4 max-w-xl text-base leading-relaxed text-brand-muted md:text-lg">
            Кажется, эта страница ушла за займом и не вернулась. Но не переживайте — у нас есть 50+ проверенных МФО, гиды и удобный поиск, чтобы помочь вам найти то, что нужно.
          </p>

          {/* Search */}
          <form
            onSubmit={(e) => {
              e.preventDefault();
              const data = new FormData(e.currentTarget);
              const q = String(data.get("q") || "").trim();
              if (q) {
                window.location.href = `/mfo?q=${encodeURIComponent(q)}`;
              }
            }}
            className="mx-auto mt-8 flex w-full max-w-xl items-center gap-2 rounded-pill border border-brand-line bg-white p-1.5 shadow-card focus-within:border-brand-blue focus-within:shadow-hover"
          >
            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand-muted">
              <Search className="h-5 w-5" />
            </div>
            <input
              name="q"
              type="search"
              placeholder="Поиск по МФО, статьям, городам…"
              className="h-12 min-w-0 flex-1 bg-transparent text-sm font-semibold text-brand-ink outline-none placeholder:text-brand-muted"
            />
            <button
              type="submit"
              className="inline-flex h-12 shrink-0 items-center justify-center gap-1.5 rounded-pill bg-brand-green px-5 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover sm:px-6"
            >
              <span className="hidden sm:inline">Найти</span>
              <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
            </button>
          </form>

          {/* Primary CTAs */}
          <div className="mt-6 flex flex-wrap items-center justify-center gap-3">
            <Link
              to="/"
              className="inline-flex h-12 items-center justify-center gap-2 rounded-pill bg-brand-ink px-6 text-sm font-bold text-white shadow-card transition-all hover:-translate-y-0.5 hover:bg-brand-ink/90 hover:shadow-hover"
            >
              <Home className="h-4 w-4" /> На главную
            </Link>
            <Link
              to="/mfo"
              className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-6 text-sm font-bold text-brand-ink shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-blue hover:text-brand-blue hover:shadow-hover"
            >
              <Compass className="h-4 w-4" /> Открыть каталог МФО
            </Link>
          </div>
        </div>

        {/* Popular sections */}
        <div className="relative mx-auto mt-14 max-w-5xl">
          <div className="text-center">
            <div className="text-[11px] font-extrabold uppercase tracking-[0.18em] text-brand-muted">
              Популярные разделы
            </div>
          </div>
          <div className="mt-6 grid gap-4 sm:grid-cols-3">
            {popularLinks.map((l) => (
              <Link
                key={l.to}
                to={l.to}
                className="group flex items-center gap-4 rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-1 hover:border-brand-blue/40 hover:shadow-hover"
              >
                <div
                  className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br ${l.tone}`}
                >
                  <l.icon className="h-5 w-5" strokeWidth={2.2} />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="text-base font-extrabold text-brand-ink group-hover:text-brand-blue">
                    {l.label}
                  </div>
                  <div className="mt-0.5 text-xs font-semibold text-brand-muted">Перейти →</div>
                </div>
                <ArrowRight
                  className="h-4 w-4 shrink-0 text-brand-muted transition-transform group-hover:translate-x-0.5 group-hover:text-brand-blue"
                  strokeWidth={2.5}
                />
              </Link>
            ))}
          </div>

          {/* Quick chips */}
          <div className="mt-10">
            <div className="text-center text-[11px] font-extrabold uppercase tracking-[0.18em] text-brand-muted">
              Быстрые переходы
            </div>
            <div className="mt-4 flex flex-wrap items-center justify-center gap-2">
              {quickJumps.map((q) => (
                <Link
                  key={q.label}
                  to={q.to}
                  params={q.params}
                  className="inline-flex items-center gap-1.5 rounded-pill border border-brand-line bg-white px-4 py-2 text-xs font-bold text-brand-ink shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-green hover:text-brand-green hover:shadow-hover"
                >
                  <MapPin className="h-3.5 w-3.5 text-brand-muted" /> {q.label}
                </Link>
              ))}
            </div>
          </div>
        </div>
      </section>

      <SiteFooter />
    </div>
  );
}

export const Route = createRootRoute({
  head: () => ({
    meta: [
      { charSet: "utf-8" },
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { title: "Гид по стилю — МФО Агрегатор" },
      { name: "description", content: "Дизайн-система агрегатора займов: цвета, типографика, компоненты" },
      { name: "author", content: "Lovable" },
      { property: "og:title", content: "Гид по стилю — МФО Агрегатор" },
      { property: "og:description", content: "Дизайн-система агрегатора займов: цвета, типографика, компоненты" },
      { property: "og:type", content: "website" },
      { name: "twitter:card", content: "summary" },
      { name: "twitter:site", content: "@Lovable" },
      { name: "twitter:title", content: "Гид по стилю — МФО Агрегатор" },
      { name: "twitter:description", content: "Дизайн-система агрегатора займов: цвета, типографика, компоненты" },
      { property: "og:image", content: "https://pub-bb2e103a32db4e198524a2e9ed8f35b4.r2.dev/c2154e50-5957-4cce-a053-b7ba17f5cf40/id-preview-1002f70a--3aa59ffa-a851-4965-8fa1-c0744d26d727.lovable.app-1777654064912.png" },
      { name: "twitter:image", content: "https://pub-bb2e103a32db4e198524a2e9ed8f35b4.r2.dev/c2154e50-5957-4cce-a053-b7ba17f5cf40/id-preview-1002f70a--3aa59ffa-a851-4965-8fa1-c0744d26d727.lovable.app-1777654064912.png" },
    ],
    links: [
      { rel: "stylesheet", href: appCss },
      { rel: "preconnect", href: "https://fonts.googleapis.com" },
      { rel: "preconnect", href: "https://fonts.gstatic.com", crossOrigin: "anonymous" },
      {
        rel: "stylesheet",
        href: "https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap",
      },
    ],
  }),
  shellComponent: RootShell,
  component: RootComponent,
  notFoundComponent: NotFoundComponent,
});

function RootShell({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <head>
        <HeadContent />
      </head>
      <body>
        {children}
        <Scripts />
      </body>
    </html>
  );
}

function RootComponent() {
  return <Outlet />;
}
