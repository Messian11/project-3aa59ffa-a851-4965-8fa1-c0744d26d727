import { createFileRoute, Link } from "@tanstack/react-router";
import { useState } from "react";
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

const categories: { label: Category; icon: typeof BookOpen }[] = [
  { label: "Все", icon: Sparkles },
  { label: "Гайды", icon: BookOpen },
  { label: "Обзоры МФО", icon: TrendingUp },
  { label: "Сравнения", icon: Scale },
  { label: "Новости", icon: Newspaper },
  { label: "Финансовая грамотность", icon: GraduationCap },
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

/* ───────────── Page ───────────── */

function BlogIndexPage() {
  const [active, setActive] = useState<Category>("Все");
  const [query, setQuery] = useState("");
  const [page, setPage] = useState(1);

  const filtered = articles.filter((a) => {
    const matchCat = active === "Все" || a.category === active;
    const matchQ = !query || a.title.toLowerCase().includes(query.toLowerCase());
    return matchCat && matchQ;
  });

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
        <div className="relative mx-auto max-w-7xl px-4 py-16 md:py-24">
          <div className="max-w-3xl">
            <span className="inline-flex items-center gap-2 rounded-full bg-brand-amber/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-amber">
              <BookOpen className="h-3.5 w-3.5" /> Блог
            </span>
            <h1 className="mt-5 text-4xl md:text-6xl font-extrabold tracking-tight text-foreground">
              Полезные статьи <br className="hidden md:block" />
              <span className="bg-gradient-to-r from-brand-blue via-brand-green to-brand-amber bg-clip-text text-transparent">о займах</span>
            </h1>
            <p className="mt-5 text-lg md:text-xl text-muted-foreground max-w-2xl">
              Гайды, инструкции, обзоры МФО и финансовая грамотность — от экспертов Zaymi Online.
            </p>

            {/* Search */}
            <div className="mt-8 max-w-xl">
              <div className="relative">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                <input
                  type="search"
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  placeholder="Поиск по статьям..."
                  className="w-full rounded-2xl border border-border bg-card pl-12 pr-4 py-4 text-base shadow-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20 transition-all"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Category tabs */}
      <section className="sticky top-16 z-30 border-b border-border/40 bg-background/80 backdrop-blur">
        <div className="mx-auto max-w-7xl px-4">
          <div className="flex gap-2 overflow-x-auto py-4 scrollbar-none">
            {categories.map(({ label, icon: Icon }) => (
              <button
                key={label}
                onClick={() => { setActive(label); setPage(1); }}
                className={cn(
                  "inline-flex items-center gap-2 whitespace-nowrap rounded-full px-5 py-2.5 text-sm font-semibold transition-all",
                  active === label
                    ? "bg-foreground text-background shadow-md"
                    : "bg-card text-muted-foreground hover:text-foreground hover:bg-card/80 border border-border"
                )}
              >
                <Icon className="h-4 w-4" />
                {label}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Featured article */}
      {active === "Все" && !query && (
        <section className="mx-auto max-w-7xl px-4 py-12 md:py-16">
          <Link
            to="/blog/$slug"
            params={{ slug: featured.slug }}
            className="group block overflow-hidden rounded-3xl border border-border bg-card shadow-lg hover:shadow-2xl transition-all"
          >
            <div className="grid md:grid-cols-2">
              <div className={cn("relative aspect-[16/10] md:aspect-auto bg-gradient-to-br flex items-center justify-center", featured.gradient)}>
                <span className="text-9xl drop-shadow-lg">{featured.emoji}</span>
                <div className="absolute top-6 left-6">
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-background/90 backdrop-blur px-3 py-1.5 text-xs font-bold text-foreground shadow">
                    <Sparkles className="h-3.5 w-3.5 text-brand-amber" /> Главная статья
                  </span>
                </div>
              </div>
              <div className="p-8 md:p-12 flex flex-col justify-center">
                <span className="inline-flex w-fit items-center rounded-full bg-brand-blue/10 px-3 py-1 text-xs font-bold text-brand-blue uppercase tracking-wide">
                  {featured.category}
                </span>
                <h2 className="mt-4 text-2xl md:text-4xl font-extrabold tracking-tight text-foreground group-hover:text-brand-blue transition-colors">
                  {featured.title}
                </h2>
                <p className="mt-4 text-base md:text-lg text-muted-foreground leading-relaxed">
                  {featured.excerpt}
                </p>
                <div className="mt-6 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                  <div className="flex items-center gap-2">
                    <div className="h-9 w-9 rounded-full bg-gradient-to-br from-brand-blue to-brand-green flex items-center justify-center text-xs font-bold text-white">
                      АП
                    </div>
                    <span className="font-medium text-foreground">{featured.author}</span>
                  </div>
                  <span className="flex items-center gap-1.5"><Calendar className="h-4 w-4" />{featured.date}</span>
                  <span className="flex items-center gap-1.5"><Clock className="h-4 w-4" />{featured.read}</span>
                </div>
                <div className="mt-8">
                  <span className="inline-flex items-center gap-2 rounded-2xl bg-foreground px-6 py-3.5 text-sm font-bold text-background group-hover:gap-3 transition-all">
                    Читать статью <ArrowRight className="h-4 w-4" />
                  </span>
                </div>
              </div>
            </div>
          </Link>
        </section>
      )}

      {/* Articles grid */}
      <section className="mx-auto max-w-7xl px-4 pb-16">
        <div className="flex items-end justify-between mb-8">
          <div>
            <h2 className="text-2xl md:text-3xl font-extrabold tracking-tight text-foreground">
              {active === "Все" ? "Все статьи" : active}
            </h2>
            <p className="mt-1 text-sm text-muted-foreground">
              Найдено: {filtered.length} {filtered.length === 1 ? "статья" : "статей"}
            </p>
          </div>
        </div>

        {filtered.length === 0 ? (
          <div className="rounded-3xl border border-border bg-card p-12 text-center">
            <p className="text-muted-foreground">По вашему запросу ничего не найдено.</p>
          </div>
        ) : (
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {filtered.map((a) => (
              <Link
                key={a.slug}
                to="/blog/$slug"
                params={{ slug: a.slug }}
                className="group flex flex-col overflow-hidden rounded-3xl border border-border bg-card shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all"
              >
                <div className={cn("relative aspect-[16/10] bg-gradient-to-br flex items-center justify-center", a.gradient)}>
                  <span className="text-7xl drop-shadow-lg">{a.emoji}</span>
                  <div className="absolute top-4 left-4">
                    <span className="inline-flex items-center rounded-full bg-background/90 backdrop-blur px-3 py-1 text-xs font-bold text-foreground shadow">
                      {a.category}
                    </span>
                  </div>
                </div>
                <div className="flex flex-1 flex-col p-6">
                  <h3 className="text-lg font-bold text-foreground group-hover:text-brand-blue transition-colors line-clamp-2">
                    {a.title}
                  </h3>
                  <p className="mt-2 text-sm text-muted-foreground line-clamp-3 flex-1">
                    {a.excerpt}
                  </p>
                  <div className="mt-5 flex items-center justify-between text-xs text-muted-foreground border-t border-border/60 pt-4">
                    <span className="font-medium text-foreground">{a.author}</span>
                    <span className="flex items-center gap-1"><Clock className="h-3.5 w-3.5" />{a.read}</span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}

        {/* Pagination */}
        {filtered.length > 0 && (
          <div className="mt-12 flex items-center justify-center gap-2">
            <button
              onClick={() => setPage(Math.max(1, page - 1))}
              disabled={page === 1}
              className="inline-flex items-center gap-1 rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-card/80 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
              <ChevronLeft className="h-4 w-4" /> Назад
            </button>
            {[1, 2, 3, 4].map((n) => (
              <button
                key={n}
                onClick={() => setPage(n)}
                className={cn(
                  "h-10 w-10 rounded-xl text-sm font-bold transition-all",
                  page === n
                    ? "bg-foreground text-background shadow-md"
                    : "bg-card text-muted-foreground hover:text-foreground border border-border"
                )}
              >
                {n}
              </button>
            ))}
            <span className="px-2 text-muted-foreground">...</span>
            <button className="h-10 w-10 rounded-xl text-sm font-bold bg-card text-muted-foreground hover:text-foreground border border-border transition-all">
              12
            </button>
            <button
              onClick={() => setPage(page + 1)}
              className="inline-flex items-center gap-1 rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-card/80 transition-all"
            >
              Вперёд <ChevronRight className="h-4 w-4" />
            </button>
          </div>
        )}
      </section>

      {/* Category deep-dives */}
      <section className="border-t border-border/40 bg-gradient-to-b from-card/30 to-background py-16 md:py-20">
        <div className="mx-auto max-w-7xl px-4">
          <div className="text-center max-w-2xl mx-auto mb-12">
            <span className="inline-flex items-center gap-2 rounded-full bg-brand-green/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-green">
              Категории
            </span>
            <h2 className="mt-4 text-3xl md:text-4xl font-extrabold tracking-tight text-foreground">
              Углубитесь в тему
            </h2>
            <p className="mt-3 text-muted-foreground">
              Выберите категорию и изучите её до конца — мы собрали всё в одном месте.
            </p>
          </div>

          <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {deepDives.map(({ title, count, gradient, icon: Icon, description }) => (
              <button
                key={title}
                className="group relative overflow-hidden rounded-3xl border border-border bg-card p-6 text-left shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all"
              >
                <div className={cn("inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-lg", gradient)}>
                  <Icon className="h-7 w-7" />
                </div>
                <h3 className="mt-5 text-lg font-bold text-foreground">{title}</h3>
                <p className="mt-1.5 text-sm text-muted-foreground">{description}</p>
                <div className="mt-5 flex items-center justify-between">
                  <span className="text-xs font-bold text-muted-foreground uppercase tracking-wider">{count} статей</span>
                  <ArrowRight className="h-4 w-4 text-foreground group-hover:translate-x-1 transition-transform" />
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
