import { createFileRoute, Link, useNavigate } from "@tanstack/react-router";
import { zodValidator, fallback } from "@tanstack/zod-adapter";
import { z } from "zod";
import { useMemo, useState, useEffect } from "react";
import {
  Search as SearchIcon,
  X,
  Star,
  ArrowRight,
  ArrowLeft,
  SlidersHorizontal,
  MapPin,
  Wallet,
  Sparkles,
  Frown,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { cn } from "@/lib/utils";

/* ───────────── Data (mirrors /mfo) ───────────── */

interface MFO {
  name: string;
  slug: string;
  rating: number;
  reviews: number;
  badges: { label: string; tone: "amber" | "green" | "blue" }[];
  amount: string;
  amountMax: number; // numeric, parsed
  term: string;
  rate: string;
  approval: string;
  letter: string;
  bg: string;
  cities: string[];
}

const ALL_CITIES = ["Москва", "Санкт-Петербург", "Екатеринбург", "Новосибирск", "Казань", "Краснодар"] as const;

const rawMfos: Omit<MFO, "amountMax">[] = [
  { slug: "zaymer", name: "Займер", rating: 4.8, reviews: 2384, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "Топ выбор", tone: "green" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "95%", letter: "З", bg: "from-brand-blue to-brand-green", cities: [...ALL_CITIES] },
  { slug: "webbankir", name: "Webbankir", rating: 4.7, reviews: 4128, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "До 100 000", tone: "blue" }], amount: "до 100 000 ₽", term: "до 168 дней", rate: "от 0%", approval: "92%", letter: "W", bg: "from-brand-amber to-brand-green", cities: [...ALL_CITIES] },
  { slug: "migcredit", name: "МигКредит", rating: 4.6, reviews: 1873, badges: [{ label: "На карту 24/7", tone: "green" }], amount: "до 50 000 ₽", term: "до 168 дней", rate: "от 0,8%", approval: "88%", letter: "М", bg: "from-brand-blue to-brand-blue/60", cities: ["Москва", "Санкт-Петербург", "Екатеринбург", "Новосибирск"] },
  { slug: "lime-zaim", name: "Лайм-Займ", rating: 4.5, reviews: 1564, badges: [{ label: "Без отказа", tone: "green" }], amount: "до 70 000 ₽", term: "до 168 дней", rate: "от 1%", approval: "90%", letter: "Л", bg: "from-brand-green to-brand-green/60", cities: [...ALL_CITIES] },
  { slug: "ezaem", name: "EzaemOnline", rating: 4.4, reviews: 982, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "Срочно", tone: "green" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "94%", letter: "E", bg: "from-brand-amber to-brand-blue", cities: ["Москва", "Санкт-Петербург", "Казань"] },
  { slug: "viva", name: "VIVA Деньги", rating: 4.3, reviews: 3128, badges: [{ label: "С плохой КИ", tone: "green" }], amount: "до 30 000 ₽", term: "до 60 дней", rate: "от 0,9%", approval: "87%", letter: "V", bg: "from-brand-blue to-brand-amber", cities: [...ALL_CITIES] },
  { slug: "turbozaym", name: "Турбозайм", rating: 4.5, reviews: 2244, badges: [{ label: "Срочно за 5 мин", tone: "amber" }], amount: "до 15 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "93%", letter: "Т", bg: "from-brand-green to-brand-blue", cities: ["Москва", "Санкт-Петербург", "Краснодар"] },
  { slug: "credit-plus", name: "Кредит Плюс", rating: 4.2, reviews: 1450, badges: [{ label: "На карту", tone: "green" }], amount: "до 50 000 ₽", term: "до 90 дней", rate: "от 0,8%", approval: "85%", letter: "К", bg: "from-brand-amber to-brand-green", cities: [...ALL_CITIES] },
  { slug: "moneyman", name: "MoneyMan", rating: 4.6, reviews: 3502, badges: [{ label: "Первый займ 0%", tone: "amber" }, { label: "До 80 000", tone: "blue" }], amount: "до 80 000 ₽", term: "до 126 дней", rate: "от 0%", approval: "91%", letter: "M", bg: "from-brand-blue to-brand-amber", cities: [...ALL_CITIES] },
  { slug: "dobrozaym", name: "ДоброЗайм", rating: 4.4, reviews: 712, badges: [{ label: "Без отказа", tone: "green" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "89%", letter: "Д", bg: "from-brand-green to-brand-blue", cities: ["Москва", "Екатеринбург", "Новосибирск"] },
  { slug: "smart-credit", name: "Smart Credit", rating: 4.3, reviews: 894, badges: [{ label: "Без справок", tone: "green" }], amount: "до 100 000 ₽", term: "до 365 дней", rate: "от 0,7%", approval: "84%", letter: "S", bg: "from-brand-amber to-brand-blue", cities: [...ALL_CITIES] },
  { slug: "creditter", name: "Creditter", rating: 4.2, reviews: 612, badges: [{ label: "Решение 5 мин", tone: "amber" }], amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0,99%", approval: "86%", letter: "C", bg: "from-brand-blue to-brand-green", cities: ["Москва", "Санкт-Петербург", "Казань", "Краснодар"] },
];

const mfos: MFO[] = rawMfos.map((m) => ({
  ...m,
  amountMax: Number((m.amount.match(/[\d\s]+/)?.[0] ?? "0").replace(/\s/g, "")) || 0,
}));

const AMOUNT_OPTIONS = [
  { value: 0, label: "Любая" },
  { value: 5000, label: "от 5 000 ₽" },
  { value: 15000, label: "от 15 000 ₽" },
  { value: 30000, label: "от 30 000 ₽" },
  { value: 50000, label: "от 50 000 ₽" },
  { value: 100000, label: "от 100 000 ₽" },
] as const;

const SORT_OPTIONS = [
  { value: "rating", label: "По рейтингу" },
  { value: "amount", label: "По сумме (макс)" },
  { value: "reviews", label: "По отзывам" },
] as const;

const PAGE_SIZE = 6;

/* ───────────── Route ───────────── */

const searchSchema = z.object({
  q: fallback(z.string(), "").default(""),
  city: fallback(z.string(), "").default(""),
  amount: fallback(z.number(), 0).default(0),
  sort: fallback(z.enum(["rating", "amount", "reviews"]), "rating").default("rating"),
  page: fallback(z.number().int().min(1).max(100), 1).default(1),
});

export const Route = createFileRoute("/search")({
  validateSearch: zodValidator(searchSchema),
  head: ({ match }) => {
    const q = (match.search as { q?: string }).q;
    const title = q ? `Поиск: «${q}» — Zaymi Online` : "Поиск по МФО — Zaymi Online";
    return {
      meta: [
        { title },
        { name: "description", content: "Поиск МФО, статей и подборок. Фильтры по городу, сумме и рейтингу." },
        { name: "robots", content: "noindex, follow" },
        { property: "og:title", content: title },
      ],
    };
  },
  component: SearchPage,
});

/* ───────────── Page ───────────── */

function SearchPage() {
  const { q, city, amount, sort, page } = Route.useSearch();
  const navigate = useNavigate({ from: "/search" });
  const [draftQ, setDraftQ] = useState(q);
  const [filtersOpen, setFiltersOpen] = useState(false);

  // sync local input when URL changes (e.g., back/forward)
  useEffect(() => setDraftQ(q), [q]);

  const filtered = useMemo(() => {
    const needle = q.trim().toLowerCase();
    let list = mfos.filter((m) => {
      if (needle) {
        const hay = [m.name, ...m.badges.map((b) => b.label)].join(" ").toLowerCase();
        if (!hay.includes(needle)) return false;
      }
      if (city && !m.cities.includes(city)) return false;
      if (amount > 0 && m.amountMax < amount) return false;
      return true;
    });
    if (sort === "rating") list = [...list].sort((a, b) => b.rating - a.rating);
    if (sort === "amount") list = [...list].sort((a, b) => b.amountMax - a.amountMax);
    if (sort === "reviews") list = [...list].sort((a, b) => b.reviews - a.reviews);
    return list;
  }, [q, city, amount, sort]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
  const safePage = Math.min(page, totalPages);
  const pageItems = filtered.slice((safePage - 1) * PAGE_SIZE, safePage * PAGE_SIZE);

  const activeFilters = [
    q && { key: "q", label: `«${q}»` },
    city && { key: "city", label: city },
    amount > 0 && { key: "amount", label: AMOUNT_OPTIONS.find((a) => a.value === amount)?.label ?? "" },
  ].filter(Boolean) as { key: "q" | "city" | "amount"; label: string }[];

  const updateSearch = (next: Partial<z.infer<typeof searchSchema>>) => {
    navigate({
      search: (prev: z.infer<typeof searchSchema>) => ({ ...prev, ...next, page: next.page ?? 1 }),
    });
  };

  const clearAll = () => {
    setDraftQ("");
    navigate({ search: { q: "", city: "", amount: 0, sort: "rating", page: 1 } });
  };

  return (
    <div className="min-h-screen bg-white">
      <SiteHeader />

      {/* Hero / Search */}
      <section className="relative overflow-hidden border-b border-brand-line/60 bg-gradient-to-br from-brand-soft via-white to-white px-5 py-12 md:px-6 md:py-16">
        <div className="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-brand-blue/10 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-brand-green/10 blur-3xl" />

        <div className="relative mx-auto max-w-4xl">
          <div className="flex flex-wrap items-center gap-2">
            <Link to="/" className="inline-flex items-center gap-1 text-xs font-bold text-brand-muted hover:text-brand-blue">
              <ArrowLeft className="h-3.5 w-3.5" /> Главная
            </Link>
            <span className="text-brand-line">/</span>
            <span className="text-xs font-bold text-brand-ink">Поиск</span>
          </div>

          <h1 className="mt-4 text-3xl font-extrabold tracking-tight text-brand-ink sm:text-4xl md:text-5xl">
            {q ? <>Результаты по запросу <span className="text-brand-green">«{q}»</span></> : "Поиск по каталогу МФО"}
          </h1>
          <p className="mt-3 text-sm text-brand-muted md:text-lg">
            Найдено <span className="font-extrabold text-brand-ink">{filtered.length}</span> {pluralize(filtered.length, "МФО", "МФО", "МФО")} • уточните фильтрами ниже
          </p>

          {/* Search bar */}
          <form
            onSubmit={(e) => {
              e.preventDefault();
              updateSearch({ q: draftQ.trim() });
            }}
            className="mt-6 flex w-full items-center gap-2 rounded-pill border border-brand-line bg-white p-1.5 shadow-card focus-within:border-brand-blue focus-within:shadow-hover"
          >
            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand-muted">
              <SearchIcon className="h-5 w-5" />
            </div>
            <input
              value={draftQ}
              onChange={(e) => setDraftQ(e.target.value)}
              type="search"
              placeholder="Название МФО, бейдж или ключевое слово…"
              className="h-12 min-w-0 flex-1 bg-transparent text-sm font-semibold text-brand-ink outline-none placeholder:text-brand-muted"
            />
            {draftQ && (
              <button
                type="button"
                onClick={() => {
                  setDraftQ("");
                  updateSearch({ q: "" });
                }}
                aria-label="Очистить"
                className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-brand-muted transition-colors hover:bg-brand-soft hover:text-brand-ink"
              >
                <X className="h-4 w-4" />
              </button>
            )}
            <button
              type="submit"
              className="inline-flex h-12 shrink-0 items-center justify-center gap-1.5 rounded-pill bg-brand-green px-5 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover sm:px-6"
            >
              <span className="hidden sm:inline">Найти</span>
              <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
            </button>
          </form>

          {/* Active filter chips */}
          {activeFilters.length > 0 && (
            <div className="mt-4 flex flex-wrap items-center gap-2">
              <span className="text-xs font-bold uppercase tracking-wider text-brand-muted">Фильтры:</span>
              {activeFilters.map((f) => (
                <button
                  key={f.key}
                  onClick={() => {
                    if (f.key === "q") setDraftQ("");
                    updateSearch({ [f.key]: f.key === "amount" ? 0 : "" } as Partial<z.infer<typeof searchSchema>>);
                  }}
                  className="inline-flex items-center gap-1.5 rounded-pill bg-white px-3 py-1.5 text-xs font-bold text-brand-ink ring-1 ring-inset ring-brand-line transition-all hover:ring-brand-blue"
                >
                  {f.label}
                  <X className="h-3 w-3" />
                </button>
              ))}
              <button
                onClick={clearAll}
                className="text-xs font-bold text-brand-blue underline-offset-2 hover:underline"
              >
                Сбросить всё
              </button>
            </div>
          )}
        </div>
      </section>

      {/* Body: filters + results */}
      <section className="px-5 py-10 md:px-6 md:py-14">
        <div className="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[260px_1fr] lg:gap-10">
          {/* Mobile filter toggle */}
          <button
            onClick={() => setFiltersOpen((o) => !o)}
            className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white text-sm font-bold text-brand-ink shadow-card lg:hidden"
          >
            <SlidersHorizontal className="h-4 w-4" />
            {filtersOpen ? "Скрыть фильтры" : "Фильтры"}
            {activeFilters.length > 0 && (
              <span className="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-green px-1.5 text-[11px] font-extrabold text-white">
                {activeFilters.length}
              </span>
            )}
          </button>

          {/* Filters */}
          <aside className={cn("space-y-4", filtersOpen ? "block" : "hidden lg:block")}>
            <div className="lg:sticky lg:top-24 space-y-4">
              <FilterCard title="Город" icon={MapPin}>
                <SelectButtons
                  options={[{ value: "", label: "Любой" }, ...ALL_CITIES.map((c) => ({ value: c, label: c }))]}
                  value={city}
                  onChange={(v) => updateSearch({ city: v })}
                />
              </FilterCard>

              <FilterCard title="Сумма займа" icon={Wallet}>
                <SelectButtons
                  options={AMOUNT_OPTIONS.map((o) => ({ value: String(o.value), label: o.label }))}
                  value={String(amount)}
                  onChange={(v) => updateSearch({ amount: Number(v) })}
                />
              </FilterCard>

              <FilterCard title="Сортировка" icon={Sparkles}>
                <SelectButtons
                  options={SORT_OPTIONS.map((o) => ({ value: o.value, label: o.label }))}
                  value={sort}
                  onChange={(v) => updateSearch({ sort: v as "rating" | "amount" | "reviews" })}
                />
              </FilterCard>
            </div>
          </aside>

          {/* Results */}
          <div className="min-w-0">
            {pageItems.length === 0 ? (
              <EmptyState onReset={clearAll} />
            ) : (
              <>
                <div className="mb-4 flex items-center justify-between text-xs font-bold text-brand-muted">
                  <span>
                    Показано {pageItems.length} из {filtered.length}
                  </span>
                  <span className="hidden sm:inline">
                    Стр. {safePage} из {totalPages}
                  </span>
                </div>

                <ul className="space-y-3">
                  {pageItems.map((m, i) => (
                    <ResultCard key={m.slug} m={m} rank={(safePage - 1) * PAGE_SIZE + i + 1} />
                  ))}
                </ul>

                {totalPages > 1 && (
                  <Pagination
                    page={safePage}
                    totalPages={totalPages}
                    onChange={(p) => updateSearch({ page: p })}
                  />
                )}
              </>
            )}
          </div>
        </div>
      </section>

      <SiteFooter />
    </div>
  );
}

/* ───────────── Subcomponents ───────────── */

function ResultCard({ m, rank }: { m: MFO; rank: number }) {
  return (
    <li className="group relative flex flex-col gap-4 rounded-2xl border border-brand-line bg-white p-4 shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-blue/40 hover:shadow-hover sm:p-5 md:grid md:grid-cols-[44px_64px_1fr_auto] md:items-center md:gap-5">
      <div className="flex items-center gap-3 md:contents">
        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-soft text-sm font-black text-brand-ink ring-1 ring-inset ring-brand-line md:h-11 md:w-11">
          #{rank}
        </div>
        <div className={cn("flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-extrabold text-white shadow-card", m.bg)}>
          {m.letter}
        </div>
        <div className="ml-auto inline-flex shrink-0 items-center gap-1 whitespace-nowrap rounded-pill bg-brand-amber/15 px-2.5 py-1 text-sm font-extrabold text-brand-ink ring-1 ring-inset ring-brand-amber/30 md:hidden">
          <Star className="h-3.5 w-3.5 fill-brand-amber text-brand-amber" /> {m.rating}
        </div>
      </div>

      <div className="min-w-0">
        <div className="flex flex-wrap items-center gap-2">
          <Link to="/mfo/$slug" params={{ slug: m.slug }} className="text-lg font-extrabold text-brand-ink hover:text-brand-blue">
            {m.name}
          </Link>
          <div className="hidden items-center gap-1 whitespace-nowrap rounded-pill bg-brand-amber/15 px-2 py-0.5 text-xs font-extrabold text-brand-ink ring-1 ring-inset ring-brand-amber/30 md:inline-flex">
            <Star className="h-3 w-3 fill-brand-amber text-brand-amber" /> {m.rating}
            <span className="text-brand-muted">· {m.reviews}</span>
          </div>
        </div>
        <div className="mt-1.5 flex flex-wrap gap-1.5">
          {m.badges.map((b) => (
            <span
              key={b.label}
              className={cn(
                "inline-flex items-center rounded-pill px-2 py-0.5 text-[11px] font-bold",
                b.tone === "amber" && "bg-brand-amber/15 text-[#9a6300] ring-1 ring-inset ring-brand-amber/30",
                b.tone === "green" && "bg-brand-green/12 text-brand-green ring-1 ring-inset ring-brand-green/25",
                b.tone === "blue" && "bg-brand-blue/10 text-brand-blue ring-1 ring-inset ring-brand-blue/20",
              )}
            >
              {b.label}
            </span>
          ))}
        </div>
        <p className="mt-2 text-xs font-bold text-brand-muted">
          {m.amount} • <span className="text-brand-green">{m.rate}</span> • {m.term} • одобрение {m.approval}
        </p>
      </div>

      <Link
        to="/mfo/$slug"
        params={{ slug: m.slug }}
        className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-pill bg-brand-green px-5 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover md:w-auto"
      >
        Подробнее <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" strokeWidth={2.5} />
      </Link>
    </li>
  );
}

function FilterCard({
  title,
  icon: Icon,
  children,
}: {
  title: string;
  icon: typeof MapPin;
  children: React.ReactNode;
}) {
  return (
    <div className="rounded-2xl border border-brand-line bg-white p-4 shadow-card">
      <div className="mb-3 flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-brand-muted">
        <Icon className="h-3.5 w-3.5" /> {title}
      </div>
      {children}
    </div>
  );
}

function SelectButtons({
  options,
  value,
  onChange,
}: {
  options: readonly { value: string; label: string }[];
  value: string;
  onChange: (v: string) => void;
}) {
  return (
    <div className="flex flex-wrap gap-1.5">
      {options.map((o) => {
        const active = o.value === value;
        return (
          <button
            key={o.value}
            onClick={() => onChange(o.value)}
            className={cn(
              "inline-flex items-center rounded-pill px-3 py-1.5 text-xs font-bold transition-all",
              active
                ? "bg-brand-green text-white shadow-card"
                : "bg-brand-soft text-brand-ink ring-1 ring-inset ring-brand-line hover:ring-brand-blue",
            )}
          >
            {o.label}
          </button>
        );
      })}
    </div>
  );
}

function Pagination({
  page,
  totalPages,
  onChange,
}: {
  page: number;
  totalPages: number;
  onChange: (p: number) => void;
}) {
  const pages = useMemo(() => {
    const arr: (number | "...")[] = [];
    const push = (n: number | "...") => arr.push(n);
    const window = 1;
    for (let i = 1; i <= totalPages; i++) {
      if (i === 1 || i === totalPages || (i >= page - window && i <= page + window)) {
        push(i);
      } else if (arr[arr.length - 1] !== "...") {
        push("...");
      }
    }
    return arr;
  }, [page, totalPages]);

  return (
    <nav className="mt-8 flex items-center justify-center gap-2" aria-label="Пагинация">
      <button
        onClick={() => onChange(Math.max(1, page - 1))}
        disabled={page === 1}
        className="inline-flex h-10 items-center justify-center gap-1 rounded-pill border border-brand-line bg-white px-4 text-xs font-bold text-brand-ink shadow-card transition-all hover:border-brand-blue hover:text-brand-blue disabled:cursor-not-allowed disabled:opacity-40"
      >
        <ArrowLeft className="h-3.5 w-3.5" /> <span className="hidden sm:inline">Назад</span>
      </button>
      <div className="flex items-center gap-1">
        {pages.map((p, i) =>
          p === "..." ? (
            <span key={`e${i}`} className="px-2 text-sm font-bold text-brand-muted">
              …
            </span>
          ) : (
            <button
              key={p}
              onClick={() => onChange(p)}
              className={cn(
                "inline-flex h-10 min-w-10 items-center justify-center rounded-full px-3 text-sm font-extrabold transition-all",
                p === page
                  ? "bg-brand-ink text-white shadow-card"
                  : "bg-white text-brand-ink ring-1 ring-inset ring-brand-line hover:ring-brand-blue",
              )}
            >
              {p}
            </button>
          ),
        )}
      </div>
      <button
        onClick={() => onChange(Math.min(totalPages, page + 1))}
        disabled={page === totalPages}
        className="inline-flex h-10 items-center justify-center gap-1 rounded-pill border border-brand-line bg-white px-4 text-xs font-bold text-brand-ink shadow-card transition-all hover:border-brand-blue hover:text-brand-blue disabled:cursor-not-allowed disabled:opacity-40"
      >
        <span className="hidden sm:inline">Вперёд</span> <ArrowRight className="h-3.5 w-3.5" />
      </button>
    </nav>
  );
}

function EmptyState({ onReset }: { onReset: () => void }) {
  return (
    <div className="rounded-3xl border border-brand-line bg-brand-soft p-8 text-center shadow-card md:p-12">
      <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-brand-muted shadow-card">
        <Frown className="h-8 w-8" />
      </div>
      <h3 className="mt-4 text-xl font-extrabold text-brand-ink md:text-2xl">Ничего не найдено</h3>
      <p className="mx-auto mt-2 max-w-md text-sm text-brand-muted md:text-base">
        Попробуйте изменить запрос или сбросить фильтры. Возможно, нужная вам МФО есть в каталоге.
      </p>
      <div className="mt-5 flex flex-wrap items-center justify-center gap-3">
        <button
          onClick={onReset}
          className="inline-flex h-12 items-center justify-center gap-2 rounded-pill bg-brand-green px-6 text-sm font-bold text-white shadow-card hover:bg-brand-green/90 hover:shadow-hover"
        >
          Сбросить фильтры
        </button>
        <Link
          to="/mfo"
          className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-6 text-sm font-bold text-brand-ink shadow-card hover:border-brand-blue hover:text-brand-blue"
        >
          Открыть весь каталог МФО <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
        </Link>
      </div>
    </div>
  );
}

function pluralize(n: number, one: string, few: string, many: string) {
  const mod10 = n % 10;
  const mod100 = n % 100;
  if (mod10 === 1 && mod100 !== 11) return one;
  if (mod10 >= 2 && mod10 <= 4 && (mod100 < 12 || mod100 > 14)) return few;
  return many;
}
