import { createFileRoute, Link } from "@tanstack/react-router";
import { useMemo, useState } from "react";
import {
  ChevronRight,
  Search,
  ArrowRight,
  Clock,
  Calendar,
  ChevronLeft,
  BookOpen,
  TrendingUp,
  Scale,
  Newspaper,
  GraduationCap,
  Sparkles,
  X,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { cn } from "@/lib/utils";

export const Route = createFileRoute("/blog/")({
  head: () => ({
    meta: [
      { title: "Блог Zaymi Online — гайды, новости и обзоры МФО 2026" },
      {
        name: "description",
        content:
          "Блог Zaymi Online: экспертные гайды по займам, обзоры МФО, сравнения, новости рынка и финансовая грамотность. Свежие статьи 2026 года.",
      },
      { property: "og:title", content: "Блог Zaymi Online — статьи о займах и МФО" },
      {
        property: "og:description",
        content: "Гайды, обзоры, сравнения и новости рынка микрофинансирования. Обновляется каждую неделю.",
      },
    ],
  }),
  component: BlogIndexPage,
});

/* ───────────── Data ───────────── */

type Category = "Все" | "Гайды" | "Обзоры МФО" | "Сравнения" | "Новости" | "Финансовая грамотность";

const categories: { label: Category; icon: typeof BookOpen; tone: string }[] = [
  { label: "Все", icon: Sparkles, tone: "text-foreground" },
  { label: "Гайды", icon: BookOpen, tone: "text-brand-blue" },
  { label: "Обзоры МФО", icon: TrendingUp, tone: "text-brand-green" },
  { label: "Сравнения", icon: Scale, tone: "text-brand-amber" },
  { label: "Новости", icon: Newspaper, tone: "text-brand-blue" },
  { label: "Финансовая грамотность", icon: GraduationCap, tone: "text-brand-green" },
];

interface Article {
  slug: string;
  category: Exclude<Category, "Все">;
  title: string;
  excerpt: string;
  author: string;
  date: string;
  read: string;
  gradient: string;
  emoji: string;
}

const featured: Article = {
  slug: "kak-vybrat-mfo",
  category: "Гайды",
  title: "Как выбрать МФО в 2026 году — пошаговый гид от экспертов",
  excerpt:
    "Разбираем по полочкам: на что смотреть в договоре, как проверить лицензию ЦБ, какие подводные камни встречаются у популярных МФО и как не переплатить лишнего. Подробная инструкция с примерами и чек-листом.",
  author: "Анна Петрова",
  date: "29 апреля 2026",
  read: "8 мин",
  gradient: "from-brand-blue via-brand-green to-brand-amber",
  emoji: "📘",
};

const articles: Article[] = [
  {
    slug: "pervyi-zaim-pod-0",
    category: "Гайды",
    title: "Первый займ под 0% — как получить и не переплатить ни рубля",
    excerpt: "Полный гид по бесплатным займам: какие МФО реально дают 0%, как вернуть вовремя и что будет при просрочке.",
    author: "Анна Петрова", date: "27 апреля 2026", read: "6 мин",
    gradient: "from-brand-amber to-brand-green", emoji: "🎁",
  },
  {
    slug: "obzor-zaymer",
    category: "Обзоры МФО",
    title: "Обзор Займер — честный отзыв после 6 месяцев использования",
    excerpt: "Тестируем Займер: скорость одобрения, реальные ставки, удобство приложения и работа поддержки.",
    author: "Игорь Соколов", date: "25 апреля 2026", read: "10 мин",
    gradient: "from-brand-blue to-brand-blue/60", emoji: "🔍",
  },
  {
    slug: "zaymer-vs-webbankir",
    category: "Сравнения",
    title: "Займер vs Webbankir — кто выгоднее в 2026 году?",
    excerpt: "Сравниваем два топовых МФО по 12 параметрам: ставки, лимиты, скорость, поддержка и пользовательский опыт.",
    author: "Мария Орлова", date: "23 апреля 2026", read: "7 мин",
    gradient: "from-brand-amber to-brand-blue", emoji: "⚖️",
  },
  {
    slug: "novyi-zakon-2026",
    category: "Новости",
    title: "Новый закон о МФО 2026 — что изменится для заёмщиков",
    excerpt: "ЦБ РФ вводит новые правила для микрофинансовых организаций. Разбираем последствия для обычных клиентов.",
    author: "Дмитрий Волков", date: "22 апреля 2026", read: "5 мин",
    gradient: "from-brand-green to-brand-blue", emoji: "📰",
  },
  {
    slug: "kreditnaya-istoriya",
    category: "Финансовая грамотность",
    title: "Что такое кредитная история и как её улучшить за 3 месяца",
    excerpt: "Простыми словами о КИ: кто и зачем её ведёт, как проверить бесплатно и какие шаги реально работают.",
    author: "Анна Петрова", date: "20 апреля 2026", read: "9 мин",
    gradient: "from-brand-green to-brand-amber", emoji: "📊",
  },
  {
    slug: "zaim-bez-otkaza",
    category: "Гайды",
    title: "Займы без отказа — миф или реальность?",
    excerpt: "Разбираемся, действительно ли существуют МФО, которые одобряют всем, и как повысить шансы на одобрение.",
    author: "Игорь Соколов", date: "18 апреля 2026", read: "6 мин",
    gradient: "from-brand-amber to-brand-amber/60", emoji: "✅",
  },
  {
    slug: "obzor-lime-zaim",
    category: "Обзоры МФО",
    title: "Обзор Лайм-Займ — стоит ли брать в 2026?",
    excerpt: "Подробный обзор условий Лайм-Займ: реальные ставки, скрытые комиссии и отзывы клиентов за последний год.",
    author: "Мария Орлова", date: "16 апреля 2026", read: "8 мин",
    gradient: "from-brand-green to-brand-green/60", emoji: "🍋",
  },
  {
    slug: "mfo-vs-bank",
    category: "Сравнения",
    title: "МФО или банк — где брать займ выгоднее в 2026",
    excerpt: "Развёрнутое сравнение двух подходов: ставки, скорость, требования к заёмщику и реальные сценарии использования.",
    author: "Дмитрий Волков", date: "14 апреля 2026", read: "11 мин",
    gradient: "from-brand-blue to-brand-amber", emoji: "🏦",
  },
  {
    slug: "kak-ne-popast-v-dolgi",
    category: "Финансовая грамотность",
    title: "Как не попасть в долговую яму при использовании МФО",
    excerpt: "5 правил безопасного пользования микрозаймами и сигналы того, что пора остановиться.",
    author: "Анна Петрова", date: "12 апреля 2026", read: "7 мин",
    gradient: "from-brand-blue to-brand-green", emoji: "⚠️",
  },
];

const deepDives = [
  { title: "Все гайды", count: 47, gradient: "from-brand-blue to-brand-green", icon: BookOpen, description: "Пошаговые инструкции по займам и МФО" },
  { title: "Обзоры МФО", count: 32, gradient: "from-brand-amber to-brand-green", icon: TrendingUp, description: "Честные обзоры популярных МФО" },
  { title: "Сравнения", count: 18, gradient: "from-brand-green to-brand-blue", icon: Scale, description: "Сравнения двух и более МФО" },
  { title: "Финграмотность", count: 24, gradient: "from-brand-amber to-brand-blue", icon: GraduationCap, description: "Учимся управлять деньгами" },
];

const PAGE_SIZE = 9;

function initials(name: string) {
  return name.split(" ").map((s) => s[0]).slice(0, 2).join("").toUpperCase();
}

/* ───────────── Page ───────────── */

function BlogIndexPage() {
  const [active, setActive] = useState<Category>("Все");
  const [query, setQuery] = useState("");
  const [page, setPage] = useState(1);

  const filtered = useMemo(() => {
    return articles.filter((a) => {
      const matchCat = active === "Все" || a.category === active;
      const matchQ = !query || a.title.toLowerCase().includes(query.toLowerCase());
      return matchCat && matchQ;
    });
  }, [active, query]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
  const currentPage = Math.min(page, totalPages);
  const visible = filtered.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

  return (
    <div className="min-h-screen bg-background">
      <SiteHeader />

      {/* Breadcrumbs */}
      <nav className="border-b border-border/40 bg-card/30">
        <div className="mx-auto max-w-7xl px-4 py-3 text-sm">
          <ol className="flex items-center gap-1.5 text-muted-foreground">
            <li><Link to="/" className="hover:text-foreground transition-colors">Главная</Link></li>
            <ChevronRight className="h-3.5 w-3.5" />
            <li className="text-foreground font-medium">Блог</li>
          </ol>
        </div>
      </nav>

      {/* Hero */}
      <section className="relative overflow-hidden border-b border-border/40">
        <div className="absolute inset-0 bg-gradient-to-br from-brand-blue/10 via-transparent to-brand-green/10" />
        <div
          aria-hidden
          className="pointer-events-none absolute -top-32 -right-32 h-[420px] w-[420px] rounded-full bg-gradient-to-br from-brand-amber/30 via-brand-green/20 to-transparent blur-3xl"
        />
        <div
          aria-hidden
          className="pointer-events-none absolute -bottom-24 -left-24 h-[320px] w-[320px] rounded-full bg-gradient-to-br from-brand-blue/30 to-transparent blur-3xl"
        />
        <div className="relative mx-auto max-w-7xl px-4 py-12 sm:py-16 md:py-24">
          <div className="max-w-3xl">
            <span className="inline-flex items-center gap-2 rounded-full bg-brand-amber/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-amber ring-1 ring-brand-amber/20">
              <BookOpen className="h-3.5 w-3.5" /> Блог · {articles.length + 1} статей
            </span>
            <h1 className="mt-5 text-[2rem] leading-[1.05] sm:text-5xl md:text-6xl font-extrabold tracking-tight text-foreground">
              Полезные статьи <br className="hidden md:block" />
              <span className="bg-gradient-to-r from-brand-blue via-brand-green to-brand-amber bg-clip-text text-transparent">
                о займах
              </span>
            </h1>
            <p className="mt-4 sm:mt-5 text-base sm:text-lg md:text-xl text-muted-foreground max-w-2xl">
              Гайды, инструкции, обзоры МФО и финансовая грамотность — от экспертов Zaymi Online.
            </p>

            {/* Search */}
            <div className="mt-6 sm:mt-8 max-w-xl">
              <div className="relative">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                <input
                  type="search"
                  value={query}
                  onChange={(e) => { setQuery(e.target.value); setPage(1); }}
                  placeholder="Поиск по статьям..."
                  className="w-full rounded-2xl border border-border bg-card pl-12 pr-12 py-3.5 sm:py-4 text-base shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/15 transition-all"
                />
                {query && (
                  <button
                    onClick={() => setQuery("")}
                    aria-label="Очистить"
                    className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition"
                  >
                    <X className="h-4 w-4" />
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Category tabs */}
      <section className="sticky top-16 z-30 border-b border-border/40 bg-background/85 backdrop-blur supports-[backdrop-filter]:bg-background/70">
        <div className="relative mx-auto max-w-7xl">
          <div
            aria-hidden
            className="pointer-events-none absolute inset-y-0 left-0 w-6 bg-gradient-to-r from-background to-transparent md:hidden"
          />
          <div
            aria-hidden
            className="pointer-events-none absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-background to-transparent md:hidden"
          />
          <div className="flex gap-2 overflow-x-auto py-3 sm:py-4 px-4 scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none]">
            {categories.map(({ label, icon: Icon, tone }) => {
              const isActive = active === label;
              return (
                <button
                  key={label}
                  onClick={() => { setActive(label); setPage(1); }}
                  className={cn(
                    "inline-flex items-center gap-2 whitespace-nowrap rounded-full px-4 sm:px-5 py-2.5 text-sm font-semibold transition-all",
                    isActive
                      ? "bg-foreground text-background shadow-md scale-[1.02]"
                      : "bg-card text-muted-foreground hover:text-foreground hover:bg-card/80 border border-border"
                  )}
                >
                  <Icon className={cn("h-4 w-4", !isActive && tone)} />
                  {label}
                </button>
              );
            })}
          </div>
        </div>
      </section>

      {/* Featured article */}
      {active === "Все" && !query && (
        <section className="mx-auto max-w-7xl px-4 py-10 sm:py-12 md:py-16">
          <Link
            to="/blog/$slug"
            params={{ slug: featured.slug }}
            className="group relative block overflow-hidden rounded-3xl border border-border bg-card shadow-lg hover:shadow-2xl transition-all"
          >
            <div className="grid md:grid-cols-2">
              <div className={cn("relative aspect-[16/10] md:aspect-auto md:min-h-[420px] bg-gradient-to-br flex items-center justify-center overflow-hidden", featured.gradient)}>
                {/* decorative blobs */}
                <div aria-hidden className="absolute -top-10 -left-10 h-48 w-48 rounded-full bg-white/15 blur-2xl" />
                <div aria-hidden className="absolute -bottom-12 -right-12 h-56 w-56 rounded-full bg-black/10 blur-3xl" />
                <span className="relative text-7xl sm:text-8xl md:text-9xl drop-shadow-xl transition-transform duration-500 group-hover:scale-110">
                  {featured.emoji}
                </span>
                <div className="absolute top-4 left-4 sm:top-6 sm:left-6">
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-background/95 backdrop-blur px-3 py-1.5 text-xs font-bold text-foreground shadow ring-1 ring-black/5">
                    <Sparkles className="h-3.5 w-3.5 text-brand-amber" /> Главная статья
                  </span>
                </div>
                <div className="absolute bottom-4 right-4 sm:bottom-6 sm:right-6">
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-foreground/85 backdrop-blur px-3 py-1.5 text-xs font-bold text-background">
                    <Clock className="h-3.5 w-3.5" /> {featured.read}
                  </span>
                </div>
              </div>
              <div className="p-6 sm:p-8 md:p-12 flex flex-col justify-center">
                <span className="inline-flex w-fit items-center rounded-full bg-brand-blue/10 px-3 py-1 text-xs font-bold text-brand-blue uppercase tracking-wide">
                  {featured.category}
                </span>
                <h2 className="mt-4 text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-foreground group-hover:text-brand-blue transition-colors">
                  {featured.title}
                </h2>
                <p className="mt-3 sm:mt-4 text-sm sm:text-base md:text-lg text-muted-foreground leading-relaxed">
                  {featured.excerpt}
                </p>
                <div className="mt-5 sm:mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground">
                  <div className="flex items-center gap-2">
                    <div className="h-9 w-9 rounded-full bg-gradient-to-br from-brand-blue to-brand-green flex items-center justify-center text-xs font-bold text-white shadow-sm">
                      {initials(featured.author)}
                    </div>
                    <span className="font-medium text-foreground">{featured.author}</span>
                  </div>
                  <span className="flex items-center gap-1.5"><Calendar className="h-4 w-4" />{featured.date}</span>
                </div>
                <div className="mt-7 sm:mt-8">
                  <span className="inline-flex items-center gap-2 rounded-2xl bg-foreground px-5 sm:px-6 py-3 sm:py-3.5 text-sm font-bold text-background group-hover:gap-3 transition-all shadow-md">
                    Читать статью <ArrowRight className="h-4 w-4" />
                  </span>
                </div>
              </div>
            </div>
          </Link>
        </section>
      )}

      {/* Articles grid */}
      <section className="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 pt-2 md:pt-0">
        <div className="flex flex-wrap items-end justify-between gap-3 mb-6 sm:mb-8">
          <div>
            <h2 className="text-2xl md:text-3xl font-extrabold tracking-tight text-foreground">
              {active === "Все" && !query ? "Все статьи" : query ? `Результаты поиска` : active}
            </h2>
            <p className="mt-1 text-sm text-muted-foreground">
              Найдено: {filtered.length} {filtered.length === 1 ? "статья" : filtered.length < 5 && filtered.length !== 0 ? "статьи" : "статей"}
            </p>
          </div>
          {(active !== "Все" || query) && (
            <button
              onClick={() => { setActive("Все"); setQuery(""); setPage(1); }}
              className="inline-flex items-center gap-1.5 rounded-full border border-border bg-card px-4 py-2 text-xs font-semibold text-muted-foreground hover:text-foreground hover:bg-card/70 transition"
            >
              <X className="h-3.5 w-3.5" /> Сбросить фильтры
            </button>
          )}
        </div>

        {filtered.length === 0 ? (
          <div className="rounded-3xl border border-dashed border-border bg-card/40 p-10 sm:p-16 text-center">
            <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-muted">
              <Search className="h-6 w-6 text-muted-foreground" />
            </div>
            <p className="text-base font-semibold text-foreground">Ничего не найдено</p>
            <p className="mt-1 text-sm text-muted-foreground">Попробуйте изменить запрос или категорию.</p>
          </div>
        ) : (
          <div className="grid gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {visible.map((a) => (
              <Link
                key={a.slug}
                to="/blog/$slug"
                params={{ slug: a.slug }}
                className="group flex flex-col overflow-hidden rounded-3xl border border-border bg-card shadow-sm hover:shadow-2xl hover:-translate-y-1 hover:border-brand-blue/30 transition-all duration-300"
              >
                <div className={cn("relative aspect-[16/10] bg-gradient-to-br flex items-center justify-center overflow-hidden", a.gradient)}>
                  {/* decorative shapes */}
                  <div aria-hidden className="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/15 blur-2xl" />
                  <div aria-hidden className="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-black/10 blur-2xl" />
                  <span className="relative text-6xl sm:text-7xl drop-shadow-lg transition-transform duration-500 group-hover:scale-110">
                    {a.emoji}
                  </span>
                  <div className="absolute top-3 left-3 sm:top-4 sm:left-4">
                    <span className="inline-flex items-center rounded-full bg-background/95 backdrop-blur px-3 py-1 text-[11px] sm:text-xs font-bold text-foreground shadow ring-1 ring-black/5">
                      {a.category}
                    </span>
                  </div>
                  <div className="absolute top-3 right-3 sm:top-4 sm:right-4">
                    <span className="inline-flex items-center gap-1 rounded-full bg-foreground/80 backdrop-blur px-2.5 py-1 text-[11px] font-bold text-background">
                      <Clock className="h-3 w-3" /> {a.read}
                    </span>
                  </div>
                </div>
                <div className="flex flex-1 flex-col p-5 sm:p-6">
                  <h3 className="text-base sm:text-lg font-bold text-foreground group-hover:text-brand-blue transition-colors line-clamp-2 leading-snug">
                    {a.title}
                  </h3>
                  <p className="mt-2 text-sm text-muted-foreground line-clamp-3 flex-1">
                    {a.excerpt}
                  </p>
                  <div className="mt-5 flex items-center justify-between gap-3 border-t border-border/60 pt-4">
                    <div className="flex items-center gap-2 min-w-0">
                      <div className="h-8 w-8 shrink-0 rounded-full bg-gradient-to-br from-brand-blue to-brand-green flex items-center justify-center text-[10px] font-bold text-white">
                        {initials(a.author)}
                      </div>
                      <span className="truncate text-xs font-semibold text-foreground">{a.author}</span>
                    </div>
                    <span className="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-brand-blue">
                      Читать <ArrowRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}

        {/* Pagination */}
        {filtered.length > PAGE_SIZE && (
          <div className="mt-10 sm:mt-12 flex flex-wrap items-center justify-center gap-2">
            <button
              onClick={() => setPage(Math.max(1, currentPage - 1))}
              disabled={currentPage === 1}
              className="inline-flex items-center gap-1 rounded-xl border border-border bg-card px-3 sm:px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-card/80 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
              <ChevronLeft className="h-4 w-4" /> <span className="hidden sm:inline">Назад</span>
            </button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((n) => (
              <button
                key={n}
                onClick={() => setPage(n)}
                className={cn(
                  "h-10 w-10 rounded-xl text-sm font-bold transition-all",
                  currentPage === n
                    ? "bg-foreground text-background shadow-md scale-105"
                    : "bg-card text-muted-foreground hover:text-foreground border border-border"
                )}
              >
                {n}
              </button>
            ))}
            <button
              onClick={() => setPage(Math.min(totalPages, currentPage + 1))}
              disabled={currentPage === totalPages}
              className="inline-flex items-center gap-1 rounded-xl border border-border bg-card px-3 sm:px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-card/80 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
              <span className="hidden sm:inline">Вперёд</span> <ChevronRight className="h-4 w-4" />
            </button>
          </div>
        )}
      </section>

      {/* Category deep-dives */}
      <section className="relative border-t border-border/40 bg-gradient-to-b from-card/40 to-background py-14 sm:py-16 md:py-20 overflow-hidden">
        <div aria-hidden className="pointer-events-none absolute -top-20 left-1/2 -translate-x-1/2 h-72 w-[640px] max-w-[90%] rounded-full bg-gradient-to-r from-brand-blue/15 via-brand-green/15 to-brand-amber/15 blur-3xl" />
        <div className="relative mx-auto max-w-7xl px-4">
          <div className="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
            <span className="inline-flex items-center gap-2 rounded-full bg-brand-green/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-green ring-1 ring-brand-green/20">
              Категории
            </span>
            <h2 className="mt-4 text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-foreground">
              Углубитесь в тему
            </h2>
            <p className="mt-3 text-sm sm:text-base text-muted-foreground">
              Выберите категорию и изучите её до конца — мы собрали всё в одном месте.
            </p>
          </div>

          <div className="grid gap-4 sm:gap-5 grid-cols-2 lg:grid-cols-4">
            {deepDives.map(({ title, count, gradient, icon: Icon, description }) => (
              <button
                key={title}
                onClick={() => {
                  const map: Record<string, Category> = {
                    "Все гайды": "Гайды",
                    "Обзоры МФО": "Обзоры МФО",
                    "Сравнения": "Сравнения",
                    "Финграмотность": "Финансовая грамотность",
                  };
                  setActive(map[title] ?? "Все");
                  setPage(1);
                  if (typeof window !== "undefined") window.scrollTo({ top: 0, behavior: "smooth" });
                }}
                className="group relative overflow-hidden rounded-3xl border border-border bg-card p-5 sm:p-6 text-left shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-brand-blue/30 transition-all"
              >
                <div className={cn("inline-flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-lg", gradient)}>
                  <Icon className="h-6 w-6 sm:h-7 sm:w-7" />
                </div>
                <h3 className="mt-4 sm:mt-5 text-base sm:text-lg font-bold text-foreground">{title}</h3>
                <p className="mt-1.5 text-xs sm:text-sm text-muted-foreground line-clamp-2">{description}</p>
                <div className="mt-4 sm:mt-5 flex items-center justify-between">
                  <span className="text-[10px] sm:text-xs font-bold text-muted-foreground uppercase tracking-wider">{count} статей</span>
                  <ArrowRight className="h-4 w-4 text-foreground transition-transform group-hover:translate-x-1" />
                </div>
              </button>
            ))}
          </div>
        </div>
      </section>

      <SiteFooter />
    </div>
  );
}
