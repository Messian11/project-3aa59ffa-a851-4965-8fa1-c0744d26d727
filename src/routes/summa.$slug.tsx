import { createFileRoute, Link } from "@tanstack/react-router";
import { createContext, useContext, useState } from "react";
import {
  ArrowRight,
  ChevronRight,
  Star,
  Wallet,
  Calendar,
  Percent,
  CheckCircle2,
  ShoppingCart,
  Fuel,
  Pill,
  GraduationCap,
  Sandwich,
  Gift,
  FileText,
  CreditCard,
  Send,
  Plus,
  Minus,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { Slider } from "@/components/ui/slider";
import { cn } from "@/lib/utils";

const FORMAT = (n: number) => n.toLocaleString("ru-RU") + " ₽";

const ALL_AMOUNTS = [1000, 3000, 5000, 7000, 10000, 15000, 20000, 30000, 50000, 100000];

function parseAmount(slug: string): number {
  const m = slug.match(/(\d+)/);
  const n = m ? parseInt(m[1], 10) : 5000;
  if (!Number.isFinite(n) || n < 1000) return 5000;
  if (n > 1_000_000) return 5000;
  return n;
}

function termRange(amount: number): { min: number; max: number; def: number } {
  if (amount <= 5000) return { min: 7, max: 30, def: 14 };
  if (amount <= 15000) return { min: 7, max: 60, def: 21 };
  if (amount <= 30000) return { min: 14, max: 90, def: 30 };
  if (amount <= 50000) return { min: 30, max: 180, def: 60 };
  return { min: 30, max: 365, def: 90 };
}

function termLabel(amount: number): string {
  const { min, max } = termRange(amount);
  return `от ${min} до ${max} дней`;
}

export const Route = createFileRoute("/summa/$slug")({
  head: ({ params }) => {
    const amount = parseAmount(params.slug);
    const f = FORMAT(amount);
    return {
      meta: [
        { title: `Займ ${f} онлайн на карту — ТОП МФО 2026 | Zaymi Online` },
        {
          name: "description",
          content: `Займ ${f} онлайн на карту за 5 минут. Проверенные МФО с лицензией ЦБ РФ. Первый займ под 0%, одобрение до 95%, без справок и поручителей.`,
        },
        { property: "og:title", content: `Займ ${f} онлайн — лучшие МФО 2026` },
        {
          property: "og:description",
          content: `Сравните МФО, которые выдают ${f}. Ставки от 0%, деньги за 5 минут.`,
        },
      ],
    };
  },
  component: AmountPage,
});

const otherAmounts = ALL_AMOUNTS;

const topMfos = [
  { name: "Займер", slug: "zaymer", letter: "З", bg: "from-brand-blue to-brand-green", rating: 4.8, reviews: 2384, line: "Выдаёт 5 000 ₽ на 30 дней под 0% (первый займ)", overpay: 0, total: 5000, badge: "Первый займ 0%" },
  { name: "Webbankir", slug: "webbankir", letter: "W", bg: "from-brand-amber to-brand-green", rating: 4.7, reviews: 4128, line: "Выдаёт 5 000 ₽ на 30 дней под 0% новым клиентам", overpay: 0, total: 5000, badge: "Решение 5 мин" },
  { name: "MoneyMan", slug: "moneyman", letter: "M", bg: "from-brand-blue to-brand-amber", rating: 4.6, reviews: 3502, line: "Выдаёт 5 000 ₽ на 30 дней — ставка 0,99%", overpay: 1485, total: 6485, badge: "На карту 24/7" },
  { name: "Лайм-Займ", slug: "lime-zaim", letter: "Л", bg: "from-brand-green to-brand-green/60", rating: 4.5, reviews: 1564, line: "Выдаёт 5 000 ₽ на 30 дней — ставка 1%", overpay: 1500, total: 6500, badge: "Без отказа" },
  { name: "Турбозайм", slug: "turbozaym", letter: "Т", bg: "from-brand-green to-brand-blue", rating: 4.5, reviews: 2244, line: "Выдаёт 5 000 ₽ на 14 дней — решение за 5 минут", overpay: 693, total: 5693, badge: "Срочно" },
];

const useCases = [
  { icon: ShoppingCart, label: "Срочные покупки", desc: "Бытовая техника, одежда, мелкий ремонт по дому." },
  { icon: Fuel, label: "Заправить машину", desc: "Полный бак до зарплаты или важной поездки." },
  { icon: Pill, label: "Лекарства до зарплаты", desc: "Когда здоровье не может ждать оплаты труда." },
  { icon: GraduationCap, label: "Учебные расходы", desc: "Учебники, курсы, школьные взносы и канцтовары." },
  { icon: Sandwich, label: "Дотянуть до зарплаты", desc: "Продукты и базовые расходы в конце месяца." },
  { icon: Gift, label: "Подарки и праздники", desc: "Дни рождения, юбилеи, праздничный стол." },
];

const steps = [
  { n: 1, icon: FileText, title: "Заполните заявку", desc: "Укажите сумму 5 000 ₽, срок и паспортные данные. Занимает 3 минуты." },
  { n: 2, icon: CheckCircle2, title: "Дождитесь решения", desc: "Автоматическая проверка занимает 1–5 минут. Уведомление придёт в SMS." },
  { n: 3, icon: CreditCard, title: "Получите деньги", desc: "5 000 ₽ зачисляются на карту любого банка мгновенно." },
];

const calcExamples = [
  { term: "7 дней", rate: "0,99%", over: 346, total: 5346 },
  { term: "14 дней", rate: "0,99%", over: 693, total: 5693 },
  { term: "30 дней", rate: "0,99%", over: 1485, total: 6485 },
  { term: "30 дней", rate: "0% (первый займ)", over: 0, total: 5000, hl: true },
];

const fullCatalog = [
  { name: "Займер", slug: "zaymer", letter: "З", bg: "from-brand-blue to-brand-green", rating: 4.8, reviews: 2384, term: "до 30 дней", rate: "от 0%", approval: "95%", badges: [{ l: "Первый 0%", t: "amber" as const }] },
  { name: "Webbankir", slug: "webbankir", letter: "W", bg: "from-brand-amber to-brand-green", rating: 4.7, reviews: 4128, term: "до 168 дней", rate: "от 0%", approval: "92%", badges: [{ l: "Первый 0%", t: "amber" as const }] },
  { name: "MoneyMan", slug: "moneyman", letter: "M", bg: "from-brand-blue to-brand-amber", rating: 4.6, reviews: 3502, term: "до 126 дней", rate: "от 0%", approval: "91%", badges: [{ l: "До 80 000", t: "blue" as const }] },
  { name: "Лайм-Займ", slug: "lime-zaim", letter: "Л", bg: "from-brand-green to-brand-green/60", rating: 4.5, reviews: 1564, term: "до 168 дней", rate: "от 1%", approval: "90%", badges: [{ l: "Без отказа", t: "green" as const }] },
  { name: "МигКредит", slug: "migcredit", letter: "М", bg: "from-brand-blue to-brand-blue/60", rating: 4.6, reviews: 1873, term: "до 168 дней", rate: "от 0,8%", approval: "88%", badges: [{ l: "На карту", t: "green" as const }] },
  { name: "Турбозайм", slug: "turbozaym", letter: "Т", bg: "from-brand-green to-brand-blue", rating: 4.5, reviews: 2244, term: "до 30 дней", rate: "от 1%", approval: "93%", badges: [{ l: "Срочно", t: "amber" as const }] },
  { name: "VIVA Деньги", slug: "viva", letter: "V", bg: "from-brand-blue to-brand-amber", rating: 4.3, reviews: 3128, term: "до 60 дней", rate: "от 0,9%", approval: "87%", badges: [{ l: "С плохой КИ", t: "green" as const }] },
  { name: "EzaemOnline", slug: "ezaem", letter: "E", bg: "from-brand-amber to-brand-blue", rating: 4.4, reviews: 982, term: "до 30 дней", rate: "от 0%", approval: "94%", badges: [{ l: "Первый 0%", t: "amber" as const }] },
];

const faqs = [
  { q: "Дадут ли мне 5 000 ₽ с плохой кредитной историей?", a: "Да, большинство МФО из нашего списка работают с заёмщиками с любой КИ. Сумма 5 000 ₽ относится к малым займам — одобрение по ним достигает 95%." },
  { q: "Можно ли получить 5 000 ₽ без отказа?", a: "Гарантировать 100% одобрение не может ни одна МФО (это запрещено законом). Но при подаче заявок в 3–5 организаций одновременно вероятность получения практически стопроцентная." },
  { q: "За какое время приходят 5 000 ₽ на карту?", a: "От 1 до 15 минут с момента одобрения. Зачисление на карту любого российского банка происходит мгновенно — карта не должна быть кредитной." },
  { q: "Сколько переплачу за 5 000 ₽ на 14 дней?", a: "При средней ставке 0,99% в день переплата составит около 693 ₽. Если оформляете первый займ под 0% — вернёте ровно 5 000 ₽." },
  { q: "Можно ли продлить срок возврата 5 000 ₽?", a: "Да, почти все МФО предлагают пролонгацию на 7–30 дней. За продление взимается плата по тарифам компании — обычно 1–2% от суммы." },
  { q: "Что будет, если не вернуть 5 000 ₽ вовремя?", a: "Начисляется штраф (по закону — не более 20% годовых на просроченную сумму) и пени. После 90 дней долг могут передать коллекторам и информацию направят в БКИ." },
];

/* ───────────── Page ───────────── */

const AmountContext = createContext<number>(5000);
const useAmount = () => useContext(AmountContext);

function AmountPage() {
  const { slug } = Route.useParams();
  const amount = parseAmount(slug);
  const fmt = FORMAT(amount);
  const { min: tMin, max: tMax } = termRange(amount);

  return (
    <AmountContext.Provider value={amount}>
      <div className="min-h-screen scroll-smooth bg-white">
        <SiteHeader />

        {/* 1. Breadcrumbs */}
        <nav className="border-b border-brand-line/60 bg-white px-6 py-3" aria-label="Хлебные крошки">
          <ol className="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-brand-muted">
            <li><Link to="/" className="hover:text-brand-blue">Главная</Link></li>
            <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
            <li><Link to="/mfo" className="hover:text-brand-blue">Займы по сумме</Link></li>
            <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
            <li className="font-semibold text-brand-ink">Займ {fmt}</li>
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
            <div className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-green">Займ по сумме</div>
            <h1 className="mt-3 max-w-3xl text-4xl font-extrabold tracking-tight text-brand-ink md:text-5xl">
              Займ {fmt} онлайн на карту
            </h1>
            <p className="mt-4 max-w-2xl text-base text-brand-muted md:text-lg">
              Подборка МФО, которые выдают сумму {fmt}. Сравните ставки и сроки. Получите деньги за 5 минут.
            </p>

            <div className="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
              <Quick icon={Wallet} label="Сумма" value={fmt} />
              <Quick icon={Calendar} label="Сроки" value={`от ${tMin} до ${tMax} дней`} />
              <Quick icon={Percent} label="Ставка" value="от 0%" highlight />
              <Quick icon={CheckCircle2} label="Одобрение" value="до 95%" />
            </div>
          </div>
        </section>

        {/* 3. Related amounts */}
        <section className="border-b border-brand-line/60 bg-white px-6 py-6">
          <div className="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center">
            <span className="shrink-0 text-xs font-extrabold uppercase tracking-[0.14em] text-brand-muted">Другие суммы:</span>
            <div className="-mx-1 flex flex-nowrap gap-2 overflow-x-auto px-1 pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0">
              {otherAmounts.map((a) => {
                const active = a === amount;
                return (
                  <Link
                    key={a}
                    to="/summa/$slug"
                    params={{ slug: `zaim-${a}` }}
                    className={cn(
                      "shrink-0 rounded-pill px-4 py-2 text-sm font-bold shadow-card transition-all hover:-translate-y-0.5",
                      active
                        ? "border border-brand-green bg-brand-green text-white shadow-hover"
                        : "border border-brand-line bg-white text-brand-ink hover:border-brand-blue hover:text-brand-blue",
                    )}
                  >
                    {FORMAT(a)}
                  </Link>
                );
              })}
            </div>
          </div>
        </section>

        {/* 4. Top 5 */}
        <TopMfoSection />

        {/* 5. Calculator */}
        <CalculatorSection />

        {/* 6. Use cases */}
        <UseCasesSection />

        {/* 7. How to get */}
        <HowToSection />

        {/* 8. Full catalog */}
        <FullCatalogSection />

        {/* 9. Calc examples */}
        <CalcExamplesSection />

        {/* 10. SEO text */}
        <SeoTextSection />

        {/* 11. FAQ */}
        <FaqSection />

        {/* 12. Related hub */}
        <RelatedHub />

        <SiteFooter />
      </div>
    </AmountContext.Provider>
  );
}

/* ───────────── Sections ───────────── */

function Quick({ icon: Icon, label, value, highlight }: { icon: typeof Wallet; label: string; value: string; highlight?: boolean }) {
  return (
    <div className="flex items-center gap-3 rounded-2xl border border-brand-line bg-white/90 px-4 py-3 shadow-card backdrop-blur">
      <div className={cn("flex h-10 w-10 items-center justify-center rounded-xl", highlight ? "bg-brand-green/15 text-brand-green" : "bg-brand-blue/10 text-brand-blue")}>
        <Icon className="h-5 w-5" />
      </div>
      <div>
        <div className="text-[10px] font-bold uppercase tracking-wider text-brand-muted">{label}</div>
        <div className={cn("text-base font-extrabold", highlight ? "text-brand-green" : "text-brand-ink")}>{value}</div>
      </div>
    </div>
  );
}

function Stars({ rating }: { rating: number }) {
  return (
    <div className="flex items-center gap-0.5">
      {[1, 2, 3, 4, 5].map((i) => (
        <Star
          key={i}
          className={cn("h-3.5 w-3.5", i <= Math.round(rating) ? "fill-brand-amber text-brand-amber" : "fill-brand-line text-brand-line")}
        />
      ))}
    </div>
  );
}

function TopMfoSection() {
  const amount = useAmount();
  const fmt = FORMAT(amount);
  const { def: defTerm } = termRange(amount);

  // Pre-compute overpay for each top MFO based on amount
  const enriched = topMfos.map((m) => {
    let rate = 0.0099;
    let term = defTerm;
    let zero = false;
    if (m.slug === "zaymer" || m.slug === "webbankir") { rate = 0; zero = true; }
    if (m.slug === "lime-zaim") rate = 0.01;
    if (m.slug === "turbozaym") { rate = 0.0099; term = Math.min(14, defTerm); }
    const overpay = zero ? 0 : Math.round(amount * rate * term);
    return { ...m, _overpay: overpay, _total: amount + overpay, _term: term };
  });

  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          ТОП-5 МФО которые выдают {fmt}
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Отобраны по рейтингу, проценту одобрения и реальным отзывам клиентов.
        </p>

        <div className="mt-10 space-y-4">
          {enriched.map((m, i) => {
            const rankGradients = [
              "from-[#f59e0b] to-[#dc2626]",
              "from-brand-blue to-brand-green",
              "from-brand-green to-brand-green/60",
              "from-brand-blue/80 to-brand-blue/50",
              "from-brand-ink to-[#334155]",
            ];
            return (
            <article
              key={m.slug}
              className="flex flex-col gap-4 rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-0.5 hover:shadow-hover md:grid md:grid-cols-[64px_72px_1fr_auto] md:items-center md:gap-5 md:p-6"
            >
              <div className="flex items-center gap-3 md:contents">
                <div className={cn("flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-black text-white shadow-card md:h-16 md:w-16 md:text-3xl", rankGradients[i] ?? rankGradients[4])}>
                  {i + 1}
                </div>
                <div className={cn("flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-xl font-extrabold text-white shadow-card md:h-[72px] md:w-[72px] md:text-3xl", m.bg)}>
                  {m.letter}
                </div>
              </div>

              <div className="min-w-0">
                <div className="flex flex-wrap items-center gap-2 md:gap-3">
                  <h3 className="text-lg font-extrabold text-brand-ink md:text-xl">
                    <Link to="/mfo/$slug" params={{ slug: m.slug }} className="hover:text-brand-blue">{m.name}</Link>
                  </h3>
                  <span className="inline-flex items-center rounded-pill bg-brand-amber/15 px-2.5 py-1 text-[11px] font-bold text-[#9a6300] ring-1 ring-inset ring-brand-amber/30">
                    {m.badge}
                  </span>
                  <div className="flex shrink-0 items-center gap-1.5 whitespace-nowrap">
                    <Stars rating={m.rating} />
                    <span className="text-sm font-extrabold text-brand-ink">{m.rating}</span>
                    <span className="text-xs text-brand-muted">({m.reviews})</span>
                  </div>
                </div>
                <p className="mt-2 text-sm font-semibold text-brand-ink">
                  Выдаёт {fmt} на {m._term} дней{m._overpay === 0 ? " под 0% (первый займ)" : ""}
                </p>
                <p className="mt-1 text-sm text-brand-muted">
                  Переплата: <b className={m._overpay === 0 ? "text-brand-green" : "text-brand-ink"}>{FORMAT(m._overpay)}</b>
                  {" • "}К возврату: <b className="text-brand-ink">{FORMAT(m._total)}</b>
                </p>
              </div>

              <div className="flex flex-col gap-2 md:items-end">
                <button className="inline-flex h-12 w-full items-center justify-center gap-2 rounded-pill bg-brand-green px-6 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98] md:w-auto">
                  Получить {fmt} <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
                </button>
                <Link
                  to="/mfo/$slug"
                  params={{ slug: m.slug }}
                  className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-5 text-sm font-bold text-brand-ink transition-all hover:border-brand-blue hover:bg-brand-soft hover:text-brand-blue md:w-auto"
                >
                  <FileText className="h-4 w-4" strokeWidth={2.5} /> Обзор
                </Link>
              </div>
            </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}

function CalculatorSection() {
  const ctxAmount = useAmount();
  const tr = termRange(ctxAmount);
  const [amount, setAmount] = useState(ctxAmount);
  const [term, setTerm] = useState(tr.def);
  const ratePerDay = 0.0099;
  const overpay = Math.round(amount * ratePerDay * term);
  const total = amount + overpay;

  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-5xl">
        <div className="text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Калькулятор займа
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Сумма уже выставлена на {FORMAT(ctxAmount)} — можете изменить и посмотреть переплату.
          </p>
        </div>

        <div className="mt-10 rounded-3xl border border-brand-line bg-white p-6 shadow-card md:p-10">
          <div className="grid gap-8 md:grid-cols-2">
            <div>
              <div className="flex items-baseline justify-between">
                <span className="text-sm font-bold text-brand-muted">Сумма</span>
                <span className="text-2xl font-extrabold text-brand-ink">{FORMAT(amount)}</span>
              </div>
              <Slider min={1000} max={100000} step={500} value={[amount]} onValueChange={(v) => setAmount(v[0])} className="mt-4" />
              <div className="mt-2 flex justify-between text-[11px] font-bold text-brand-muted">
                <span>1 000 ₽</span><span>100 000 ₽</span>
              </div>
            </div>

            <div>
              <div className="flex items-baseline justify-between">
                <span className="text-sm font-bold text-brand-muted">Срок</span>
                <span className="text-2xl font-extrabold text-brand-ink">{term} дн.</span>
              </div>
              <Slider min={tr.min} max={tr.max} step={1} value={[term]} onValueChange={(v) => setTerm(v[0])} className="mt-4" />
              <div className="mt-2 flex justify-between text-[11px] font-bold text-brand-muted">
                <span>{tr.min} дн.</span><span>{tr.max} дн.</span>
              </div>
            </div>
          </div>

          <div className="mt-8 grid gap-4 rounded-2xl bg-brand-soft p-5 sm:grid-cols-3">
            <Stat label="Ставка" value="0,99% / день" />
            <Stat label="Переплата" value={FORMAT(overpay)} />
            <Stat label="К возврату" value={FORMAT(total)} highlight />
          </div>

          <button className="mt-6 flex h-14 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-base font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
            Получить {FORMAT(amount)} <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
          </button>
          <p className="mt-3 text-center text-xs text-brand-muted">Заявка за 5 минут • Деньги на карту любого банка</p>
        </div>
      </div>
    </section>
  );
}

function Stat({ label, value, highlight }: { label: string; value: string; highlight?: boolean }) {
  return (
    <div>
      <div className="text-[10px] font-bold uppercase tracking-wider text-brand-muted">{label}</div>
      <div className={cn("mt-1 text-xl font-extrabold", highlight ? "text-brand-green" : "text-brand-ink")}>{value}</div>
    </div>
  );
}

function UseCasesSection() {
  const fmt = FORMAT(useAmount());
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Когда нужен займ {fmt}?
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Самые частые причины обращения за небольшим займом.
        </p>

        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {useCases.map((c) => (
            <div
              key={c.label}
              className="group rounded-2xl border border-brand-line bg-white p-6 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover"
            >
              <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-green/12 text-brand-green ring-1 ring-inset ring-brand-green/20">
                <c.icon className="h-6 w-6" />
              </div>
              <h3 className="mt-4 text-lg font-extrabold text-brand-ink">{c.label}</h3>
              <p className="mt-1.5 text-sm leading-relaxed text-brand-muted">{c.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function HowToSection() {
  const fmt = FORMAT(useAmount());
  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Как получить займ {fmt} онлайн
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Три простых шага — от заявки до денег на карте.
        </p>

        <div className="mt-10 grid gap-6 md:grid-cols-3">
          {steps.map((s, i) => (
            <div key={s.n} className="relative rounded-2xl border border-brand-line bg-white p-6 shadow-card md:p-8">
              <div className="absolute -top-4 left-6 inline-flex h-9 items-center justify-center rounded-full bg-brand-green px-3.5 text-sm font-extrabold text-white shadow-card">
                Шаг {s.n}
              </div>
              <div className="mt-2 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-blue/10 text-brand-blue">
                <s.icon className="h-7 w-7" />
              </div>
              <h3 className="mt-4 text-xl font-extrabold text-brand-ink">{s.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-brand-muted">{s.desc}</p>
              {i < steps.length - 1 && (
                <Send className="absolute -right-3 top-1/2 hidden h-6 w-6 -translate-y-1/2 rotate-0 text-brand-line md:block" />
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function FullCatalogSection() {
  const amount = useAmount();
  const fmt = FORMAT(amount);
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <div className="flex items-end justify-between gap-4">
          <div>
            <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
              Все МФО для займа {fmt}
            </h2>
            <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
              Полный каталог организаций — отфильтровано по сумме.
            </p>
          </div>
          <Link to="/mfo" className="hidden text-sm font-bold text-brand-blue hover:underline md:inline">
            Все МФО →
          </Link>
        </div>

        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {fullCatalog.map((m) => (
            <article key={m.slug} className="group flex h-full flex-col rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover">
              <div className="flex items-start justify-between gap-3">
                <div className={cn("flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-extrabold text-white shadow-card", m.bg)}>
                  {m.letter}
                </div>
                <div className="flex shrink-0 flex-col items-end whitespace-nowrap" style={{ minWidth: 76 }}>
                  <Stars rating={m.rating} />
                  <div className="mt-1 text-sm font-extrabold text-brand-ink">{m.rating}</div>
                  <div className="text-[11px] font-medium text-brand-muted">({m.reviews})</div>
                </div>
              </div>

              <h3 className="mt-4 text-lg font-extrabold leading-tight text-brand-ink">
                <Link to="/mfo/$slug" params={{ slug: m.slug }} className="hover:text-brand-blue">{m.name}</Link>
              </h3>

              <div className="mt-3 flex flex-wrap gap-1.5" style={{ minHeight: 28 }}>
                {m.badges.map((b) => (
                  <span
                    key={b.l}
                    className={cn(
                      "inline-flex items-center rounded-pill px-2.5 py-1 text-[11px] font-bold ring-1 ring-inset",
                      b.t === "amber" && "bg-brand-amber/15 text-[#9a6300] ring-brand-amber/30",
                      b.t === "green" && "bg-brand-green/12 text-brand-green ring-brand-green/20",
                      b.t === "blue" && "bg-brand-blue/10 text-brand-blue ring-brand-blue/20",
                    )}
                  >
                    {b.l}
                  </span>
                ))}
              </div>

              <dl className="mt-4 space-y-2 rounded-xl bg-brand-soft p-3 text-sm">
                <Row k="Сумма" v={fmt} />
                <Row k="Срок" v={m.term} />
                <Row k="Ставка" v={m.rate} highlight />
                <Row k="Одобрение" v={m.approval} />
              </dl>

              <div className="mt-auto pt-5">
                <button className="flex h-12 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
                  Получить {fmt} <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
                </button>
                <Link
                  to="/mfo/$slug"
                  params={{ slug: m.slug }}
                  className="mt-2 flex h-11 w-full items-center justify-center gap-2 rounded-pill border border-brand-line bg-white text-sm font-bold text-brand-ink transition-all hover:border-brand-blue hover:bg-brand-soft hover:text-brand-blue"
                >
                  <FileText className="h-4 w-4" strokeWidth={2.5} /> Обзор {m.name}
                </Link>
              </div>
            </article>
          ))}
        </div>

        <Link
          to="/mfo"
          className="mt-8 flex h-12 w-full items-center justify-center gap-2 rounded-pill border border-brand-line bg-white text-sm font-bold text-brand-blue shadow-card transition-all hover:border-brand-blue hover:bg-brand-soft md:hidden"
        >
          Смотреть все МФО <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
        </Link>
      </div>
    </section>
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

function CalcExamplesSection() {
  const fmt = FORMAT(useAmount());
  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-5xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Сколько придётся вернуть?
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Примеры расчёта для займа {fmt} при разных сроках и ставках.
        </p>

        {/* Desktop table */}
        <div className="mt-8 hidden overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card md:block">
          <table className="w-full text-left">
            <thead className="bg-brand-ink text-white">
              <tr>
                <th className="px-5 py-4 text-xs font-extrabold uppercase tracking-wider">Срок</th>
                <th className="px-5 py-4 text-xs font-extrabold uppercase tracking-wider">Ставка</th>
                <th className="px-5 py-4 text-xs font-extrabold uppercase tracking-wider">Переплата</th>
                <th className="px-5 py-4 text-xs font-extrabold uppercase tracking-wider">К возврату</th>
              </tr>
            </thead>
            <tbody>
              {calcExamples.map((r, i) => (
                <tr key={i} className={cn("border-t border-brand-line", r.hl && "bg-brand-green/5")}>
                  <td className="px-5 py-4 text-sm font-bold text-brand-ink">{r.term}</td>
                  <td className="px-5 py-4 text-sm font-bold text-brand-ink">{r.rate}</td>
                  <td className={cn("px-5 py-4 text-sm font-extrabold", r.over === 0 ? "text-brand-green" : "text-brand-ink")}>{FORMAT(r.over)}</td>
                  <td className={cn("px-5 py-4 text-base font-extrabold", r.hl ? "text-brand-green" : "text-brand-ink")}>{FORMAT(r.total)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Mobile cards */}
        <div className="mt-8 space-y-3 md:hidden">
          {calcExamples.map((r, i) => (
            <div key={i} className={cn("rounded-2xl border bg-white p-4 shadow-card", r.hl ? "border-brand-green" : "border-brand-line")}>
              <div className="flex items-baseline justify-between">
                <div className="text-sm font-bold text-brand-ink">{r.term}</div>
                <div className="text-xs font-bold text-brand-muted">{r.rate}</div>
              </div>
              <div className="mt-3 grid grid-cols-2 gap-3">
                <div>
                  <div className="text-[10px] font-bold uppercase tracking-wider text-brand-muted">Переплата</div>
                  <div className={cn("mt-0.5 text-base font-extrabold", r.over === 0 ? "text-brand-green" : "text-brand-ink")}>{FORMAT(r.over)}</div>
                </div>
                <div>
                  <div className="text-[10px] font-bold uppercase tracking-wider text-brand-muted">К возврату</div>
                  <div className={cn("mt-0.5 text-base font-extrabold", r.hl ? "text-brand-green" : "text-brand-ink")}>{FORMAT(r.total)}</div>
                </div>
              </div>
            </div>
          ))}
        </div>

        <p className="mt-5 text-xs text-brand-muted">
          Расчёт ориентировочный. Точную ПСК и итоговую переплату смотрите в индивидуальных условиях договора.
        </p>
      </div>
    </section>
  );
}

function SeoTextSection() {
  const fmt = FORMAT(useAmount());
  return (
    <section className="px-6 py-20">
      <article className="mx-auto max-w-4xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Что важно знать перед получением займа {fmt}
        </h2>
        <p className="mt-5 text-base leading-relaxed text-brand-muted md:text-lg">
          Сумма 5 000 рублей — одна из самых востребованных в микрофинансировании. По статистике 2025 года, каждый третий онлайн-займ в России выдаётся именно в диапазоне 3 000–7 000 ₽. Это деньги «до зарплаты», которые помогают закрыть срочные расходы без сложных бюрократических процедур.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Кому подходит займ на 5 000 ₽</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Малый займ — самый доступный продукт МФО. Его одобряют практически всем гражданам РФ от 18 лет с действующим паспортом и активной банковской картой. Доход и трудоустройство при сумме до 10 000 ₽ обычно не проверяются: достаточно подтвердить личность и контактные данные.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Как сэкономить и взять 5 000 ₽ под 0%</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Большинство МФО предлагают новым клиентам беспроцентный первый займ. Если вы ещё не пользовались услугами конкретной компании — берёте 5 000 ₽ и возвращаете ровно 5 000 ₽. Это абсолютно легальная акция: в нашем каталоге <Link to="/mfo" className="font-bold text-brand-blue hover:underline">МФО</Link> отмечены такие предложения значком «Первый займ 0%».
        </p>
        <ul className="mt-4 space-y-2 text-brand-muted">
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Срок акции — обычно от 7 до 30 дней.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Возвращать нужно строго в день, указанный в договоре, иначе ставка пересчитается.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Повторное использование акции в той же компании невозможно.</span></li>
        </ul>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Документы и требования</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Для получения 5 000 ₽ онлайн нужен только паспорт гражданина РФ и СНИЛС (опционально). Никаких справок 2-НДФЛ, поручителей и залога. Заявка заполняется через сайт или мобильное приложение МФО — от ввода данных до зачисления денег проходит 5–15 минут.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">На какую карту поступят деньги</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          5 000 ₽ можно получить на дебетовую карту любого российского банка: Сбер, ВТБ, Тинькофф, Альфа, Газпромбанк, МКБ и десятки других. Карта должна быть оформлена на ваше имя и быть активной — кредитные карты для зачисления не подходят.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Что делать, если отказали</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Если одна МФО отказала — это не приговор. Подайте заявку в 2–3 другие организации параллельно: алгоритмы скоринга у всех разные, и почти всегда находится компания, готовая выдать малый займ. Используйте наш <Link to="/" className="font-bold text-brand-blue hover:underline">подбор займа</Link> — он отправит заявку сразу в несколько МФО.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Как погасить займ 5 000 ₽</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Погашение возможно картой через личный кабинет, по реквизитам в банковском приложении, через терминалы Qiwi, Сбербанк Онлайн или почтовым переводом. Большинство МФО не берут комиссию при оплате с карты — зачисление мгновенное. Старайтесь возвращать долг до 18:00 по московскому времени, чтобы избежать переноса даты погашения на следующий день.
        </p>

        <p className="mt-10 rounded-2xl border border-brand-line bg-brand-soft p-6 leading-relaxed text-brand-ink">
          <b>Главное:</b> 5 000 ₽ — небольшая сумма, но и к ней нужно относиться ответственно. Берите ровно столько, сколько сможете вернуть в срок — и онлайн-займ останется удобным финансовым инструментом, а не источником проблем.
        </p>
      </article>
    </section>
  );
}

function FaqSection() {
  const [open, setOpen] = useState<number | null>(0);
  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-3xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Частые вопросы про займ 5 000 ₽
        </h2>
        <p className="mt-3 text-base text-brand-muted md:text-lg">
          Ответы на самые популярные вопросы заёмщиков.
        </p>

        <div className="mt-8 space-y-3">
          {faqs.map((f, i) => {
            const isOpen = open === i;
            return (
              <div key={i} className="overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card">
                <button
                  onClick={() => setOpen(isOpen ? null : i)}
                  className="flex w-full items-center justify-between gap-4 px-5 py-4 text-left"
                >
                  <span className="text-base font-extrabold text-brand-ink">{f.q}</span>
                  <span className={cn("flex h-8 w-8 shrink-0 items-center justify-center rounded-full transition-all", isOpen ? "bg-brand-green text-white" : "bg-brand-soft text-brand-ink")}>
                    {isOpen ? <Minus className="h-4 w-4" /> : <Plus className="h-4 w-4" />}
                  </span>
                </button>
                {isOpen && (
                  <div className="border-t border-brand-line px-5 py-4 text-sm leading-relaxed text-brand-muted">
                    {f.a}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}

const relatedColumns = [
  { title: "По сумме", links: ["Займ 1 000 ₽", "Займ 3 000 ₽", "Займ 7 000 ₽", "Займ 10 000 ₽", "Займ 30 000 ₽", "Займ 100 000 ₽"] },
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
            Подберите займ под свою задачу
          </h2>
          <p className="mt-3 text-base text-white/70 md:text-lg">
            Удобная навигация по сумме, городу и жизненной ситуации.
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
