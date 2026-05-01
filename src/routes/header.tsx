import { createFileRoute } from "@tanstack/react-router";
import { SiteHeader } from "@/components/site/SiteHeader";

export const Route = createFileRoute("/header")({
  component: HeaderShowcase,
});

function HeaderShowcase() {
  return (
    <div className="min-h-screen bg-page-gradient">
      {/* Live header at the very top of the page */}
      <SiteHeader />

      <div className="mx-auto max-w-7xl px-5 py-10">
        <div className="mb-10">
          <span className="inline-flex items-center rounded-pill bg-brand-soft px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-blue">
            Site Header · Showcase
          </span>
          <h1
            className="mt-3 font-extrabold text-brand-ink"
            style={{ fontSize: "clamp(28px, 4vw, 44px)", lineHeight: 1.05 }}
          >
            Шапка сайта — десктоп и мобильная версии
          </h1>
          <p className="mt-3 max-w-2xl text-brand-muted">
            Живая шапка закреплена сверху страницы. Ниже — превью обеих версий
            бок о бок: десктоп с дропдаунами и мобильная с боковым меню.
          </p>
        </div>

        <div className="grid gap-8 lg:grid-cols-[1fr_400px]">
          {/* Desktop preview */}
          <div>
            <div className="mb-3 flex items-center gap-2">
              <span className="inline-flex items-center rounded-pill bg-brand-blue/10 px-2.5 py-1 text-xs font-bold text-brand-blue">
                Desktop ≥ 768px
              </span>
              <span className="text-xs font-medium text-brand-muted">
                логотип · навигация · поиск
              </span>
            </div>
            <div className="overflow-hidden rounded-xl border border-brand-line bg-white shadow-card">
              <div className="flex items-center gap-1.5 border-b border-brand-line bg-brand-soft px-4 py-2">
                <span className="h-2.5 w-2.5 rounded-full bg-[#ff5f56]" />
                <span className="h-2.5 w-2.5 rounded-full bg-[#ffbd2e]" />
                <span className="h-2.5 w-2.5 rounded-full bg-[#27c93f]" />
                <span className="ml-3 text-xs font-mono text-brand-muted">
                  zaymi.online
                </span>
              </div>
              <div className="bg-white">
                <SiteHeader variant="desktop" />
              </div>
              <div className="h-64 bg-hero-gradient p-6">
                <p className="text-sm font-semibold text-brand-muted">
                  ↑ наведите на «Подборки», «По сумме», «По городам» — откроются дропдауны
                </p>
              </div>
            </div>
          </div>

          {/* Mobile preview */}
          <div>
            <div className="mb-3 flex items-center gap-2">
              <span className="inline-flex items-center rounded-pill bg-brand-green/12 px-2.5 py-1 text-xs font-bold text-brand-green">
                Mobile &lt; 768px
              </span>
              <span className="text-xs font-medium text-brand-muted">
                бургер · логотип · поиск
              </span>
            </div>
            <div
              className="mx-auto overflow-hidden rounded-[2.5rem] border-[10px] border-brand-dark bg-white shadow-hover"
              style={{ width: 360 }}
            >
              <div className="flex h-6 items-center justify-center bg-brand-dark">
                <span className="h-1 w-16 rounded-full bg-white/30" />
              </div>
              <SiteHeader variant="mobile" />
              <div className="h-[520px] bg-hero-gradient p-5">
                <p className="text-sm font-semibold text-brand-muted">
                  Нажмите бургер слева — откроется полноэкранное меню.
                  Нажмите лупу справа — выпадает строка поиска.
                </p>
              </div>
            </div>
          </div>
              <div className="h-[520px] bg-hero-gradient p-5">
                <p className="text-sm font-semibold text-brand-muted">
                  Нажмите бургер слева — откроется полноэкранное меню.
                  Нажмите лупу справа — выпадает строка поиска.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div className="mt-12 grid gap-4 sm:grid-cols-3">
          <Feature title="Sticky + blur" desc="white/85 + backdrop-blur-lg, тонкая тень shadow-sticky" />
          <Feature title="Hover-дропдауны" desc="плавная анимация opacity + translate, открытие на hover" />
          <Feature title="Mobile drawer" desc="полноэкранное меню справа, аккордеон секций, крупные тапы" />
        </div>
      </div>
    </div>
  );
}

function Feature({ title, desc }: { title: string; desc: string }) {
  return (
    <div className="rounded-lg border border-brand-line bg-white p-5 shadow-card">
      <div className="text-sm font-extrabold text-brand-ink">{title}</div>
      <div className="mt-1 text-sm text-brand-muted">{desc}</div>
    </div>
  );
}
