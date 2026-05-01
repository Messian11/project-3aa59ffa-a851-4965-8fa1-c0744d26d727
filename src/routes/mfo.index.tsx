import { createFileRoute, Link } from "@tanstack/react-router";
import { useState } from "react";
import {
  ArrowRight,
  ChevronRight,
  Star,
  LayoutGrid,
  List as ListIcon,
  X,
  SlidersHorizontal,
  CheckCircle2,
  ChevronLeft,
  FileText,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { Slider } from "@/components/ui/slider";
import { cn } from "@/lib/utils";

export const Route = createFileRoute("/mfo/")({
  head: () => ({
    meta: [
      { title: "Каталог МФО — все микрофинансовые организации России 2026 | Zaymi Online" },
      {
        name: "description",
        content:
          "Полный каталог МФО России 2026: 50+ проверенных микрофинансовых организаций с лицензией ЦБ РФ. Сравните ставки, суммы и сроки — выберите лучший займ онлайн.",
      },
      { property: "og:title", content: "Каталог МФО России 2026 — 50+ организаций" },
      {
        property: "og:description",
        content: "Сравните условия 50+ МФО с лицензией ЦБ РФ. Фильтры по сумме, сроку и ставке.",
      },
    ],
  }),
  component: CatalogPage,
});

/* ───────────── Data ───────────── */

interface MFO {
  name: string;
  rating: number;
  reviews: number;
  badges: { label: string; tone: "amber" | "green" | "blue" }[];
  amount: string;
  term: string;
  rate: string;
  approval: string;
  letter: string;
  bg: string;
  slug: string;
}

const mfos: MFO[] = [
  { slug: "zaymer", name: "Займер", rating: 4.8, reviews: 2384, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "Топ выбор", tone: "green" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "95%", letter: "З", bg: "from-brand-blue to-brand-green" },
  { slug: "webbankir", name: "Webbankir", rating: 4.7, reviews: 4128, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "До 100 000", tone: "blue" }], amount: "до 100 000 ₽", term: "до 168 дней", rate: "от 0%", approval: "92%", letter: "W", bg: "from-brand-amber to-brand-green" },
  { slug: "migcredit", name: "МигКредит", rating: 4.6, reviews: 1873, badges: [{ label: "На карту 24/7", tone: "green" }], amount: "до 50 000 ₽", term: "до 168 дней", rate: "от 0,8%", approval: "88%", letter: "М", bg: "from-brand-blue to-brand-blue/60" },
  { slug: "lime-zaim", name: "Лайм-Займ", rating: 4.5, reviews: 1564, badges: [{ label: "Без отказа", tone: "green" }], amount: "до 70 000 ₽", term: "до 168 дней", rate: "от 1%", approval: "90%", letter: "Л", bg: "from-brand-green to-brand-green/60" },
  { slug: "ezaem", name: "EzaemOnline", rating: 4.4, reviews: 982, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "Срочно", tone: "green" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "94%", letter: "E", bg: "from-brand-amber to-brand-blue" },
  { slug: "viva", name: "VIVA Деньги", rating: 4.3, reviews: 3128, badges: [{ label: "С плохой КИ", tone: "green" }], amount: "до 30 000 ₽", term: "до 60 дней", rate: "от 0,9%", approval: "87%", letter: "V", bg: "from-brand-blue to-brand-amber" },
  { slug: "turbozaym", name: "Турбозайм", rating: 4.5, reviews: 2244, badges: [{ label: "Срочно за 5 мин", tone: "amber" }], amount: "до 15 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "93%", letter: "Т", bg: "from-brand-green to-brand-blue" },
  { slug: "credit-plus", name: "Кредит Плюс", rating: 4.2, reviews: 1450, badges: [{ label: "На карту", tone: "green" }], amount: "до 50 000 ₽", term: "до 90 дней", rate: "от 0,8%", approval: "85%", letter: "К", bg: "from-brand-amber to-brand-green" },
  { slug: "moneyman", name: "MoneyMan", rating: 4.6, reviews: 3502, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "До 80 000", tone: "blue" }], amount: "до 80 000 ₽", term: "до 126 дней", rate: "от 0%", approval: "91%", letter: "M", bg: "from-brand-blue to-brand-amber" },
  { slug: "dobrozaym", name: "ДоброЗайм", rating: 4.4, reviews: 712, badges: [{ label: "Без отказа", tone: "green" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "89%", letter: "Д", bg: "from-brand-green to-brand-blue" },
  { slug: "smart-credit", name: "Smart Credit", rating: 4.3, reviews: 894, badges: [{ label: "Без справок", tone: "green" }], amount: "до 100 000 ₽", term: "до 365 дней", rate: "от 0,7%", approval: "84%", letter: "S", bg: "from-brand-amber to-brand-blue" },
  { slug: "creditter", name: "Creditter", rating: 4.2, reviews: 612, badges: [{ label: "Решение 5 мин", tone: "amber" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0,99%", approval: "86%", letter: "C", bg: "from-brand-blue to-brand-green" },
];

const features = [
  "Первый займ 0%",
  "Без отказа",
  "С плохой КИ",
  "Без справок",
  "На карту любого банка",
  "Решение за 5 минут",
  "Лицензия ЦБ РФ",
];

const sortOptions = ["По рейтингу", "По сумме", "По ставке", "По одобрению"];

/* ───────────── Page ───────────── */

function CatalogPage() {
  const [view, setView] = useState<"grid" | "list">("grid");
  const [drawerOpen, setDrawerOpen] = useState(false);

  return (
    <div className="min-h-screen scroll-smooth bg-white">
      <SiteHeader />

      {/* 1. Breadcrumbs */}
      <nav className="border-b border-brand-line/60 bg-white px-6 py-3" aria-label="Хлебные крошки">
        <ol className="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-brand-muted">
          <li><Link to="/" className="hover:text-brand-blue">Главная</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li className="font-semibold text-brand-ink">Каталог МФО</li>
        </ol>
      </nav>

      {/* 2. Hero */}
      <section
        className="px-6 py-16 md:py-20"
        style={{
          background:
            "radial-gradient(circle at 90% 10%, rgba(16,185,129,0.10), transparent 40%), radial-gradient(circle at 5% 90%, rgba(37,99,235,0.10), transparent 45%), linear-gradient(180deg, #f8fafc 0%, #ffffff 100%)",
        }}
      >
        <div className="mx-auto max-w-7xl">
          <div className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-green">Все МФО</div>
          <h1 className="mt-3 max-w-3xl text-4xl font-extrabold tracking-tight text-brand-ink md:text-5xl">
            Каталог микрофинансовых организаций
          </h1>
          <p className="mt-4 max-w-2xl text-base text-brand-muted md:text-lg">
            50+ проверенных МФО с лицензией ЦБ РФ. Сравните условия и выберите лучшее предложение.
          </p>

          <div className="mt-8 flex flex-wrap gap-2 md:gap-3">
            {[
              { icon: "📊", label: "50+ МФО" },
              { icon: "✅", label: "Все с лицензией" },
              { icon: "⭐", label: "Средний рейтинг 4.7" },
              { icon: "💯", label: "Обновлено сегодня" },
            ].map((s) => (
              <span
                key={s.label}
                className="inline-flex items-center gap-2 rounded-pill border border-brand-line bg-white/90 px-4 py-2 text-sm font-bold text-brand-ink shadow-card backdrop-blur"
              >
                <span aria-hidden>{s.icon}</span> {s.label}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* 3 + 4 + 5. Filters + results */}
      <section className="px-6 py-12">
        <div className="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[300px_1fr]">
          {/* Mobile filter button */}
          <div className="lg:hidden">
            <button
              onClick={() => setDrawerOpen(true)}
              className="flex h-12 w-full items-center justify-center gap-2 rounded-pill border border-brand-line bg-white text-sm font-bold text-brand-ink shadow-card"
            >
              <SlidersHorizontal className="h-4 w-4" /> Фильтры
            </button>
          </div>

          {/* Sticky desktop sidebar */}
          <aside className="hidden lg:block">
            <div className="sticky top-24">
              <FiltersPanel />
            </div>
          </aside>

          <div>
            {/* Results header */}
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <div className="text-lg font-extrabold text-brand-ink">
                  Найдено <span className="text-brand-green">47 МФО</span>
                </div>
                <div className="mt-2 flex flex-wrap gap-1.5">
                  {["Сумма: до 30 000 ₽", "Без отказа", "Лицензия ЦБ РФ"].map((c) => (
                    <span
                      key={c}
                      className="inline-flex items-center gap-1 rounded-pill bg-brand-soft px-3 py-1 text-xs font-bold text-brand-ink ring-1 ring-inset ring-brand-line"
                    >
                      {c}
                      <X className="h-3 w-3 text-brand-muted" />
                    </span>
                  ))}
                </div>
              </div>

              <div className="flex items-center gap-2">
                <div className="inline-flex rounded-pill border border-brand-line bg-white p-1 shadow-card">
                  <button
                    onClick={() => setView("grid")}
                    aria-label="Сетка"
                    className={cn(
                      "flex h-9 w-9 items-center justify-center rounded-pill transition-all",
                      view === "grid" ? "bg-brand-ink text-white" : "text-brand-muted hover:text-brand-ink",
                    )}
                  >
                    <LayoutGrid className="h-4 w-4" />
                  </button>
                  <button
                    onClick={() => setView("list")}
                    aria-label="Список"
                    className={cn(
                      "flex h-9 w-9 items-center justify-center rounded-pill transition-all",
                      view === "list" ? "bg-brand-ink text-white" : "text-brand-muted hover:text-brand-ink",
                    )}
                  >
                    <ListIcon className="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>

            {/* Results */}
            <div className="mt-6">
              {view === "grid" ? <GridResults /> : <ListResults />}
            </div>

            {/* 6. Pagination */}
            <Pagination />
          </div>
        </div>
      </section>

      {/* 7. SEO content */}
      <SeoTextBlock />

      {/* 8. Related hub */}
      <RelatedHub />

      <SiteFooter />

      {/* Mobile filters drawer */}
      {drawerOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div className="absolute inset-0 bg-brand-ink/50 backdrop-blur-sm" onClick={() => setDrawerOpen(false)} />
          <div className="absolute inset-y-0 right-0 flex w-[88%] max-w-sm flex-col bg-white shadow-2xl">
            <div className="flex items-center justify-between border-b border-brand-line px-5 py-4">
              <div className="text-base font-extrabold text-brand-ink">Фильтры</div>
              <button onClick={() => setDrawerOpen(false)} aria-label="Закрыть" className="rounded-full p-1 text-brand-muted hover:bg-brand-soft">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="flex-1 overflow-y-auto p-5">
              <FiltersPanel />
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

/* ───────────── Filters Panel ───────────── */

function FiltersPanel() {
  const [amount, setAmount] = useState<[number, number]>([1000, 30000]);
  const [term, setTerm] = useState<[number, number]>([1, 30]);
  const [checked, setChecked] = useState<Record<string, boolean>>({
    "Лицензия ЦБ РФ": true,
    "Без отказа": true,
  });
  const [sort, setSort] = useState(sortOptions[0]);

  return (
    <div className="rounded-2xl border border-brand-line bg-white p-5 shadow-card">
      <FilterGroup title="Сумма займа">
        <Slider
          min={1000}
          max={100000}
          step={1000}
          value={amount}
          onValueChange={(v) => setAmount([v[0], v[1]] as [number, number])}
          className="mt-2"
        />
        <div className="mt-4 grid grid-cols-2 gap-2">
          <NumInput label="От" value={amount[0]} suffix="₽" />
          <NumInput label="До" value={amount[1]} suffix="₽" />
        </div>
      </FilterGroup>

      <FilterGroup title="Срок">
        <Slider
          min={1}
          max={365}
          step={1}
          value={term}
          onValueChange={(v) => setTerm([v[0], v[1]] as [number, number])}
          className="mt-2"
        />
        <div className="mt-4 grid grid-cols-2 gap-2">
          <NumInput label="От" value={term[0]} suffix="дн" />
          <NumInput label="До" value={term[1]} suffix="дн" />
        </div>
      </FilterGroup>

      <FilterGroup title="Особенности">
        <div className="mt-1 space-y-2.5">
          {features.map((f) => (
            <label key={f} className="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-brand-ink">
              <input
                type="checkbox"
                checked={!!checked[f]}
                onChange={(e) => setChecked((p) => ({ ...p, [f]: e.target.checked }))}
                className="peer sr-only"
              />
              <span
                className={cn(
                  "flex h-5 w-5 shrink-0 items-center justify-center rounded-md border transition-all",
                  checked[f]
                    ? "border-brand-green bg-brand-green text-white"
                    : "border-brand-line bg-white",
                )}
              >
                {checked[f] && <CheckCircle2 className="h-3.5 w-3.5" />}
              </span>
              {f}
            </label>
          ))}
        </div>
      </FilterGroup>

      <FilterGroup title="Ставка">
        <NumInput label="От" value={0} suffix="% / день" />
      </FilterGroup>

      <FilterGroup title="Сортировка" last>
        <div className="relative">
          <select
            value={sort}
            onChange={(e) => setSort(e.target.value)}
            className="h-11 w-full appearance-none rounded-xl border border-brand-line bg-white px-3 pr-9 text-sm font-semibold text-brand-ink focus:border-brand-blue focus:outline-none"
          >
            {sortOptions.map((o) => <option key={o}>{o}</option>)}
          </select>
          <ChevronRight className="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 rotate-90 text-brand-muted" />
        </div>
      </FilterGroup>

      <div className="mt-6 flex items-center gap-3">
        <button className="text-sm font-bold text-brand-muted hover:text-brand-ink">Сбросить</button>
        <button className="ml-auto inline-flex h-11 items-center justify-center rounded-pill bg-brand-green px-6 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover">
          Применить
        </button>
      </div>
    </div>
  );
}

function FilterGroup({ title, children, last }: { title: string; children: React.ReactNode; last?: boolean }) {
  return (
    <div className={cn("border-brand-line/70", !last && "border-b pb-5 mb-5")}>
      <div className="text-xs font-extrabold uppercase tracking-[0.14em] text-brand-muted">{title}</div>
      <div className="mt-3">{children}</div>
    </div>
  );
}

function NumInput({ label, value, suffix }: { label: string; value: number; suffix?: string }) {
  return (
    <label className="block">
      <span className="text-[10px] font-bold uppercase tracking-wider text-brand-muted">{label}</span>
      <div className="mt-1 flex h-10 items-center rounded-xl border border-brand-line bg-white px-3 focus-within:border-brand-blue">
        <input
          defaultValue={value}
          className="w-full bg-transparent text-sm font-bold text-brand-ink outline-none"
        />
        {suffix && <span className="ml-1 text-xs font-semibold text-brand-muted">{suffix}</span>}
      </div>
    </label>
  );
}

/* ───────────── Results ───────────── */

function Stars({ rating }: { rating: number }) {
  return (
    <div className="flex items-center gap-0.5">
      {[1, 2, 3, 4, 5].map((i) => (
        <Star
          key={i}
          className={cn(
            "h-3.5 w-3.5",
            i <= Math.round(rating) ? "fill-brand-amber text-brand-amber" : "fill-brand-line text-brand-line",
          )}
        />
      ))}
    </div>
  );
}

function Badge({ label, tone }: { label: string; tone: "amber" | "green" | "blue" }) {
  return (
    <span
      className={cn(
        "inline-flex items-center rounded-pill px-2.5 py-1 text-[11px] font-bold ring-1 ring-inset",
        tone === "amber" && "bg-brand-amber/15 text-[#9a6300] ring-brand-amber/30",
        tone === "green" && "bg-brand-green/12 text-brand-green ring-brand-green/20",
        tone === "blue" && "bg-brand-blue/10 text-brand-blue ring-brand-blue/20",
      )}
    >
      {label}
    </span>
  );
}

function GridResults() {
  return (
    <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
      {mfos.map((m) => (
        <article
          key={m.slug}
          className="group flex flex-col rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-hover"
        >
          <div className="flex items-start justify-between">
            <div className={cn("flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-extrabold text-white shadow-card", m.bg)}>
              {m.letter}
            </div>
            <div className="text-right">
              <Stars rating={m.rating} />
              <div className="mt-1 text-sm font-extrabold text-brand-ink">{m.rating}</div>
              <div className="text-[11px] font-medium text-brand-muted">({m.reviews} отз.)</div>
            </div>
          </div>

          <h3 className="mt-4 text-[20px] font-extrabold leading-tight text-brand-ink">
            <Link to="/mfo/$slug" params={{ slug: m.slug }} className="hover:text-brand-blue">
              {m.name}
            </Link>
          </h3>

          <div className="mt-3 flex flex-wrap gap-1.5">
            {m.badges.map((b) => <Badge key={b.label} {...b} />)}
          </div>

          <dl className="mt-4 space-y-2 rounded-xl bg-brand-soft p-3 text-sm">
            <Row k="Сумма" v={m.amount} />
            <Row k="Срок" v={m.term} />
            <Row k="Ставка" v={m.rate} highlight />
            <Row k="Одобрение" v={m.approval} />
          </dl>

          <button className="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
            Получить займ <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </button>
        </article>
      ))}
    </div>
  );
}

function ListResults() {
  return (
    <div className="space-y-4">
      {mfos.map((m) => (
        <article
          key={m.slug}
          className="rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:shadow-hover"
        >
          <div className="grid gap-5 md:grid-cols-[80px_1fr_auto] md:items-center">
            <div className={cn("flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br text-3xl font-extrabold text-white shadow-card", m.bg)}>
              {m.letter}
            </div>

            <div>
              <div className="flex flex-wrap items-center gap-3">
                <h3 className="text-xl font-extrabold text-brand-ink">
                  <Link to="/mfo/$slug" params={{ slug: m.slug }} className="hover:text-brand-blue">
                    {m.name}
                  </Link>
                </h3>
                <div className="flex flex-wrap gap-1.5">
                  {m.badges.map((b) => <Badge key={b.label} {...b} />)}
                </div>
              </div>

              <dl className="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                <MiniSpec k="Сумма" v={m.amount} />
                <MiniSpec k="Срок" v={m.term} />
                <MiniSpec k="Ставка" v={m.rate} highlight />
                <MiniSpec k="Одобрение" v={m.approval} />
              </dl>
            </div>

            <div className="flex flex-col items-stretch gap-3 md:items-end">
              <div className="flex items-center gap-2 md:flex-col md:items-end">
                <Stars rating={m.rating} />
                <div className="text-sm font-extrabold text-brand-ink">{m.rating}</div>
                <div className="text-[11px] font-medium text-brand-muted">({m.reviews})</div>
              </div>
              <button className="inline-flex h-12 items-center justify-center gap-2 rounded-pill bg-brand-green px-6 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
                Получить займ <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
              </button>
            </div>
          </div>
        </article>
      ))}
    </div>
  );
}

function Row({ k, v, highlight }: { k: string; v: string; highlight?: boolean }) {
  return (
    <div className="flex items-baseline justify-between">
      <dt className="text-xs font-semibold text-brand-muted">{k}:</dt>
      <dd className={cn("text-sm font-extrabold", highlight ? "text-brand-green" : "text-brand-ink")}>{v}</dd>
    </div>
  );
}

function MiniSpec({ k, v, highlight }: { k: string; v: string; highlight?: boolean }) {
  return (
    <div className="rounded-xl bg-brand-soft px-3 py-2">
      <div className="text-[10px] font-bold uppercase tracking-wider text-brand-muted">{k}</div>
      <div className={cn("mt-0.5 text-sm font-extrabold", highlight ? "text-brand-green" : "text-brand-ink")}>{v}</div>
    </div>
  );
}

/* ───────────── Pagination ───────────── */

function Pagination() {
  const pages = [1, 2, 3, "...", 8] as const;
  const [active, setActive] = useState(1);

  return (
    <div className="mt-12 flex flex-col items-center gap-6">
      <button className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-6 text-sm font-bold text-brand-ink shadow-card transition-all hover:border-brand-blue hover:text-brand-blue">
        Показать ещё 35 МФО <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
      </button>

      <nav className="flex items-center gap-1.5" aria-label="Постраничная навигация">
        <button className="flex h-10 w-10 items-center justify-center rounded-pill border border-brand-line bg-white text-brand-muted transition-all hover:border-brand-blue hover:text-brand-blue" aria-label="Назад">
          <ChevronLeft className="h-4 w-4" />
        </button>
        {pages.map((p, i) =>
          p === "..." ? (
            <span key={i} className="px-2 text-sm font-bold text-brand-muted">…</span>
          ) : (
            <button
              key={i}
              onClick={() => setActive(p as number)}
              className={cn(
                "flex h-10 w-10 items-center justify-center rounded-pill border text-sm font-bold transition-all",
                active === p
                  ? "border-brand-green bg-brand-green text-white shadow-card"
                  : "border-brand-line bg-white text-brand-ink hover:border-brand-blue hover:text-brand-blue",
              )}
            >
              {p}
            </button>
          ),
        )}
        <button className="flex h-10 w-10 items-center justify-center rounded-pill border border-brand-line bg-white text-brand-muted transition-all hover:border-brand-blue hover:text-brand-blue" aria-label="Вперёд">
          <ChevronRight className="h-4 w-4" />
        </button>
      </nav>
    </div>
  );
}

/* ───────────── SEO Text Block ───────────── */

function SeoTextBlock() {
  return (
    <section className="bg-brand-soft px-6 py-20">
      <article className="mx-auto max-w-4xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Как выбрать МФО — гид от экспертов Zaymi Online
        </h2>
        <p className="mt-5 text-base leading-relaxed text-brand-muted md:text-lg">
          На рынке микрофинансирования России работает более 1 000 организаций, но лишь часть из них соответствует строгим требованиям Центрального банка. Мы собрали в каталоге 50+ проверенных МФО — все они имеют действующую лицензию ЦБ РФ и работают по закону № 151-ФЗ.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">На что обращать внимание при выборе</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Главный критерий выбора МФО — наличие лицензии в реестре ЦБ РФ. Без неё компания не имеет права выдавать займы. Все МФО в нашем каталоге проверены: вы можете быть уверены в их легальности и соблюдении закона о потребительском кредитовании.
        </p>
        <ul className="mt-4 space-y-2 text-brand-muted">
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span><b className="text-brand-ink">Процентная ставка</b> — по закону не выше 0,8% в день для краткосрочных займов.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span><b className="text-brand-ink">Полная стоимость кредита (ПСК)</b> — указана крупным шрифтом в правом верхнем углу договора.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span><b className="text-brand-ink">Способы получения</b> — на карту любого банка, наличными или электронным кошельком.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span><b className="text-brand-ink">Возможность пролонгации</b> — продление срока без штрафов.</span></li>
        </ul>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Первый займ под 0% — выгодно ли это</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Многие МФО предлагают новым клиентам первый займ полностью без процентов. Это легальный маркетинговый инструмент: компания компенсирует «нулевую» прибыль за счёт привлечения лояльных клиентов. Если вы уверены, что вернёте деньги в срок — это самый выгодный способ занять. Подробнее в обзоре <Link to="/mfo/$slug" params={{ slug: "zaymer" }} className="font-bold text-brand-blue hover:underline">Займер</Link>.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Что делать при плохой кредитной истории</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Если банки отказывают, МФО — реальная альтернатива. Большинство микрофинансовых организаций не делают жёстких запросов в БКИ, а одобрение зависит от текущей платёжеспособности, а не прошлых ошибок. В нашем каталоге есть отдельный фильтр «С плохой КИ» — он отсеет только те компании, которые работают с такими заёмщиками.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Сколько МФО можно использовать одновременно</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Юридически — сколько угодно. Каждое заявление рассматривается независимо. Однако мы рекомендуем не брать займы для погашения старых: это путь к долговой яме. Используйте калькулятор займа на нашей <Link to="/" className="font-bold text-brand-blue hover:underline">главной странице</Link>, чтобы заранее оценить переплату.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Безопасность и личные данные</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Все МФО из каталога обязаны защищать персональные данные согласно ФЗ-152. Перед подачей заявки убедитесь, что сайт работает по защищённому протоколу HTTPS, а в подвале указаны реквизиты компании, ИНН, ОГРН и номер записи в реестре ЦБ РФ.
        </p>

        <p className="mt-10 rounded-2xl border border-brand-line bg-white p-6 leading-relaxed text-brand-ink">
          <b>Итог:</b> выбор МФО — это выбор между скоростью и стоимостью. Используйте фильтры каталога, чтобы найти оптимальное соотношение, читайте отзывы реальных клиентов и всегда внимательно изучайте договор перед подписанием.
        </p>
      </article>
    </section>
  );
}

/* ───────────── Related hub ───────────── */

const relatedColumns = [
  { title: "По сумме", links: ["Займ 1 000 ₽", "Займ 5 000 ₽", "Займ 10 000 ₽", "Займ 30 000 ₽", "Займ 50 000 ₽", "Займ 100 000 ₽"] },
  { title: "По городам", links: ["МФО в Москве", "МФО в СПб", "МФО в Казани", "МФО в Новосибирске", "МФО в Екатеринбурге", "Все города"] },
  { title: "По ситуации", links: ["Без отказа", "С плохой КИ", "Пенсионерам", "Студентам", "Без справок", "Срочно за 5 минут"] },
  { title: "ТОП МФО", links: ["Займер", "Webbankir", "МигКредит", "Лайм-Займ", "MoneyMan", "Турбозайм"] },
];

function RelatedHub() {
  return (
    <section
      className="px-6 py-20"
      style={{
        background:
          "radial-gradient(circle at 80% 20%, rgba(16,185,129,0.18), transparent 35%), radial-gradient(circle at 10% 90%, rgba(37,99,235,0.22), transparent 40%), linear-gradient(135deg, #0f172a 0%, #133b52 100%)",
      }}
    >
      <div className="mx-auto max-w-7xl">
        <div className="max-w-2xl">
          <h2 className="text-3xl font-extrabold tracking-tight text-white md:text-4xl">
            Найдите свой займ за 30 секунд
          </h2>
          <p className="mt-3 text-base text-white/70 md:text-lg">
            Подборки по сумме, городу и жизненной ситуации.
          </p>
        </div>

        <div className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
          {relatedColumns.map((c) => (
            <div key={c.title}>
              <h3 className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-green">{c.title}</h3>
              <ul className="mt-5 space-y-3">
                {c.links.map((l) => (
                  <li key={l}>
                    <a href="#" className="text-sm font-semibold text-white/80 transition-colors hover:text-white">{l}</a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
