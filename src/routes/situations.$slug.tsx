import { createFileRoute, Link } from "@tanstack/react-router";
import { useState } from "react";
import {
  ArrowRight,
  ChevronRight,
  Star,
  ShieldCheck,
  TrendingUp,
  Cpu,
  Sparkles,
  RefreshCw,
  ListChecks,
  CheckCircle2,
  Wallet,
  PhoneCall,
  Plus,
  Minus,
  Ban,
  Zap,
  FileX,
  FileText,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { cn } from "@/lib/utils";

export const Route = createFileRoute("/situations/$slug")({
  head: () => ({
    meta: [
      { title: "Займы без отказа онлайн — 24 МФО которые точно одобрят 2026 | Zaymi Online" },
      {
        name: "description",
        content:
          "24 МФО с одобрением 90%+. Займы без отказа онлайн на карту даже с плохой кредитной историей. Решение за 5 минут, без справок и поручителей.",
      },
      { property: "og:title", content: "Займы без отказа — 24 МФО, которые точно одобрят" },
      {
        property: "og:description",
        content: "Подборка МФО с самым высоким процентом одобрений. Деньги получат даже клиенты с плохой КИ.",
      },
    ],
  }),
  component: SituationPage,
});

const trustReasons = [
  {
    icon: TrendingUp,
    title: "Высокий процент одобрений",
    desc: "В подборке только МФО, у которых одобрение 90% и выше — по реальной статистике за 2025 год.",
  },
  {
    icon: Cpu,
    title: "Гибкий скоринг",
    desc: "Эти компании используют собственные алгоритмы оценки и не делают жёстких запросов в БКИ.",
  },
  {
    icon: ShieldCheck,
    title: "Лояльны к КИ",
    desc: "Работают с заёмщиками, у которых были просрочки, отказы банков и испорченная кредитная история.",
  },
  {
    icon: Sparkles,
    title: "Минимум требований",
    desc: "Только паспорт РФ и активная карта. Без справок 2-НДФЛ, поручителей и подтверждения дохода.",
  },
];

const situationMfos = [
  { name: "Лайм-Займ", slug: "lime-zaim", letter: "Л", bg: "from-brand-green to-brand-green/60", rating: 4.5, reviews: 1564, amount: "до 70 000 ₽", term: "до 168 дней", rate: "от 1%", approval: "96%", badges: [{ l: "Без отказа", t: "green" as const }, { l: "С плохой КИ", t: "blue" as const }] },
  { name: "Займер", slug: "zaymer", letter: "З", bg: "from-brand-blue to-brand-green", rating: 4.8, reviews: 2384, amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "95%", badges: [{ l: "Топ выбор", t: "amber" as const }, { l: "Первый 0%", t: "green" as const }] },
  { name: "EzaemOnline", slug: "ezaem", letter: "E", bg: "from-brand-amber to-brand-blue", rating: 4.4, reviews: 982, amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "94%", badges: [{ l: "Решение 5 мин", t: "amber" as const }] },
  { name: "Турбозайм", slug: "turbozaym", letter: "Т", bg: "from-brand-green to-brand-blue", rating: 4.5, reviews: 2244, amount: "до 15 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "93%", badges: [{ l: "Срочно", t: "amber" as const }] },
  { name: "Webbankir", slug: "webbankir", letter: "W", bg: "from-brand-amber to-brand-green", rating: 4.7, reviews: 4128, amount: "до 100 000 ₽", term: "до 168 дней", rate: "от 0%", approval: "92%", badges: [{ l: "Без отказа", t: "green" as const }] },
  { name: "MoneyMan", slug: "moneyman", letter: "M", bg: "from-brand-blue to-brand-amber", rating: 4.6, reviews: 3502, amount: "до 80 000 ₽", term: "до 126 дней", rate: "от 0%", approval: "91%", badges: [{ l: "Без справок", t: "green" as const }] },
  { name: "ДоброЗайм", slug: "dobrozaym", letter: "Д", bg: "from-brand-green to-brand-blue", rating: 4.4, reviews: 712, amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "90%", badges: [{ l: "Без отказа", t: "green" as const }] },
  { name: "VIVA Деньги", slug: "viva", letter: "V", bg: "from-brand-blue to-brand-amber", rating: 4.3, reviews: 3128, amount: "до 30 000 ₽", term: "до 60 дней", rate: "от 0,9%", approval: "90%", badges: [{ l: "С плохой КИ", t: "green" as const }] },
];

const refusalAdvice = [
  { icon: RefreshCw, title: "Подайте сразу в 3–5 МФО", desc: "Алгоритмы скоринга у всех разные. То, что отказала одна, не значит, что откажут все. При параллельной подаче в 5 компаний вероятность одобрения близка к 100%." },
  { icon: ListChecks, title: "Проверьте корректность данных", desc: "Опечатка в номере паспорта, неправильный СНИЛС или устаревший адрес — самые частые причины автоматического отказа. Перепроверьте всё перед подачей." },
  { icon: Wallet, title: "Уменьшите сумму займа", desc: "Если просили 30 000 ₽ — попробуйте 5 000–10 000 ₽. Малые суммы одобряют чаще, а после успешного погашения у вас откроется больший лимит." },
  { icon: PhoneCall, title: "Позвоните в службу поддержки", desc: "У многих МФО можно уточнить причину отказа и попросить ручную проверку. Часто решение пересматривают в вашу пользу." },
  { icon: CheckCircle2, title: "Улучшите кредитный рейтинг", desc: "Закройте мелкие кредитки, оплатите старые долги, оформите карту рассрочки и пользуйтесь ей. Через 2–3 месяца ситуация заметно улучшится." },
];

const relatedSituations = [
  { name: "С плохой КИ", slug: "s-plohoy-ki" },
  { name: "Пенсионерам", slug: "pensioneram" },
  { name: "Студентам", slug: "studentam" },
  { name: "Безработным", slug: "bezrabotnym" },
  { name: "Без справок", slug: "bez-spravok" },
  { name: "Срочно за 5 минут", slug: "srochno" },
];

const situationFaqs = [
  { q: "Существуют ли МФО со 100% одобрением?", a: "По закону ни одна МФО не может гарантировать 100% одобрение — это запрещено. Но реальный показатель одобрений у компаний из нашей подборки достигает 95–96%, что близко к гарантированному выдаче." },
  { q: "Дадут ли мне займ с открытыми просрочками?", a: "С активными просрочками шансы ниже, но не нулевые. Лайм-Займ, ДоброЗайм и Webbankir работают даже с такими заёмщиками — особенно если просрочка небольшая или связана с банками, а не с другими МФО." },
  { q: "Берут ли эти МФО плату за рассмотрение?", a: "Нет. Все МФО из подборки работают без комиссий за подачу заявки и рассмотрение. Платите только проценты по договору. Если у вас просят деньги «за одобрение» — это мошенники." },
  { q: "Как быстро придёт ответ?", a: "Решение приходит автоматически за 1–5 минут. В редких случаях заявка отправляется на ручную проверку — тогда ожидание может составить до 30 минут." },
  { q: "Что делать, если все МФО подряд отказывают?", a: "Это сигнал, что проблема в ваших данных или КИ. Проверьте свою кредитную историю бесплатно через Госуслуги, исправьте ошибки, оплатите хотя бы часть просроченных долгов и попробуйте снова через 30 дней." },
  { q: "Можно ли оформить займ без отказа без работы?", a: "Да, у большинства МФО из подборки официальная занятость не обязательна. Главное — указать стабильный источник дохода: пенсию, стипендию, фриланс или подработку." },
];

/* ───────────── Page ───────────── */

function SituationPage() {
  return (
    <div className="min-h-screen scroll-smooth bg-white">
      <SiteHeader />

      {/* 1. Breadcrumbs */}
      <nav className="border-b border-brand-line/60 bg-white px-6 py-3" aria-label="Хлебные крошки">
        <ol className="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-brand-muted">
          <li><Link to="/" className="hover:text-brand-blue">Главная</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li><Link to="/mfo" className="hover:text-brand-blue">Подборки</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li className="font-semibold text-brand-ink">Займы без отказа</li>
        </ol>
      </nav>

      {/* 2. Hero (warm urgency) */}
      <section
        className="relative overflow-hidden px-6 py-16 md:py-20"
        style={{
          background:
            "radial-gradient(circle at 85% 15%, rgba(245,158,11,0.20), transparent 45%), radial-gradient(circle at 5% 90%, rgba(239,68,68,0.10), transparent 50%), linear-gradient(135deg, #fff7ed 0%, #fff1f2 100%)",
        }}
      >
        <div className="mx-auto max-w-7xl">
          <div className="inline-flex items-center gap-2 rounded-pill bg-white/90 px-3 py-1.5 text-xs font-extrabold uppercase tracking-[0.18em] text-[#b45309] shadow-card backdrop-blur">
            <Sparkles className="h-3.5 w-3.5" /> Подборка МФО
          </div>
          <h1 className="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight text-brand-ink md:text-5xl">
            Займы онлайн <span className="text-[#dc2626]">без отказа</span>
          </h1>
          <p className="mt-4 max-w-2xl text-base text-brand-ink/70 md:text-lg">
            24 МФО с самым высоким процентом одобрений. Деньги получат даже клиенты с плохой кредитной историей.
          </p>

          <div className="mt-8 flex flex-wrap gap-2 md:gap-3">
            {[
              { icon: Ban, label: "95% одобрений", tone: "red" },
              { icon: CheckCircle2, label: "С плохой КИ", tone: "green" },
              { icon: Zap, label: "Решение 5 мин", tone: "amber" },
              { icon: FileX, label: "Без справок", tone: "blue" },
            ].map((s) => (
              <span
                key={s.label}
                className="inline-flex items-center gap-2 rounded-pill border border-white/60 bg-white/95 px-4 py-2 text-sm font-bold text-brand-ink shadow-card backdrop-blur"
              >
                <s.icon
                  className={cn(
                    "h-4 w-4",
                    s.tone === "red" && "text-[#dc2626]",
                    s.tone === "green" && "text-brand-green",
                    s.tone === "amber" && "text-[#b45309]",
                    s.tone === "blue" && "text-brand-blue",
                  )}
                  strokeWidth={2.5}
                />
                {s.label}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* 3. Why these MFO */}
      <WhyTrustSection />

      {/* 4. Catalog */}
      <CatalogSection />

      {/* 5. What to do if refused */}
      <RefusalAdviceSection />

      {/* 6. Related situations */}
      <RelatedSituationsSection />

      {/* SEO */}
      <SeoTextSection />

      {/* FAQ */}
      <FaqSection />

      {/* Hub */}
      <RelatedHub />

      <SiteFooter />
    </div>
  );
}

/* ───────────── Sections ───────────── */

function Stars({ rating }: { rating: number }) {
  return (
    <div className="flex items-center gap-0.5">
      {[1, 2, 3, 4, 5].map((i) => (
        <Star key={i} className={cn("h-3.5 w-3.5", i <= Math.round(rating) ? "fill-brand-amber text-brand-amber" : "fill-brand-line text-brand-line")} />
      ))}
    </div>
  );
}

function WhyTrustSection() {
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Почему именно эти МФО одобряют без отказа?
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Мы отбираем компании по 4 объективным критериям, а не по рекламным обещаниям.
        </p>

        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {trustReasons.map((r) => (
            <div key={r.title} className="rounded-2xl border border-brand-line bg-white p-6 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover">
              <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-green/12 text-brand-green ring-1 ring-inset ring-brand-green/20">
                <r.icon className="h-6 w-6" />
              </div>
              <h3 className="mt-4 text-lg font-extrabold text-brand-ink">{r.title}</h3>
              <p className="mt-1.5 text-sm leading-relaxed text-brand-muted">{r.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function CatalogSection() {
  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <div className="flex items-end justify-between gap-4">
          <div>
            <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
              МФО с самым высоким одобрением
            </h2>
            <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
              Только компании с показателем одобрения 90%+ за 2025 год.
            </p>
          </div>
          <Link to="/mfo" className="hidden text-sm font-bold text-brand-blue hover:underline md:inline">
            Все 24 МФО →
          </Link>
        </div>

        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {situationMfos.map((m) => (
            <article key={m.slug} className="group flex flex-col rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover">
              <div className="flex items-start justify-between">
                <div className={cn("flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-extrabold text-white shadow-card", m.bg)}>
                  {m.letter}
                </div>
                <div className="text-right">
                  <Stars rating={m.rating} />
                  <div className="mt-1 text-sm font-extrabold text-brand-ink">{m.rating}</div>
                  <div className="text-[11px] font-medium text-brand-muted">({m.reviews})</div>
                </div>
              </div>

              <h3 className="mt-4 text-lg font-extrabold leading-tight text-brand-ink">
                <Link to="/mfo/$slug" params={{ slug: m.slug }} className="hover:text-brand-blue">{m.name}</Link>
              </h3>

              <div className="mt-3 flex flex-wrap gap-1.5">
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
                <Row k="Сумма" v={m.amount} />
                <Row k="Срок" v={m.term} />
                <Row k="Ставка" v={m.rate} />
                <Row k="Одобрение" v={m.approval} highlight />
              </dl>

              <button className="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
                Получить займ <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
              </button>
            </article>
          ))}
        </div>
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

function RefusalAdviceSection() {
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-5xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Что делать, если всё-таки отказали?
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          5 рабочих советов, которые повышают шансы на одобрение в разы.
        </p>

        <div className="mt-10 space-y-4">
          {refusalAdvice.map((a, i) => (
            <div
              key={a.title}
              className="grid gap-5 rounded-2xl border border-brand-line bg-white p-6 shadow-card transition-all hover:shadow-hover md:grid-cols-[64px_56px_1fr] md:items-center md:p-7"
            >
              <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-ink text-2xl font-black text-white shadow-card">
                {i + 1}
              </div>
              <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-blue/10 text-brand-blue">
                <a.icon className="h-6 w-6" />
              </div>
              <div>
                <h3 className="text-lg font-extrabold text-brand-ink">{a.title}</h3>
                <p className="mt-1.5 text-sm leading-relaxed text-brand-muted">{a.desc}</p>
              </div>
            </div>
          ))}
        </div>

        <div className="mt-10 rounded-2xl border border-brand-line bg-brand-soft p-6 text-sm leading-relaxed text-brand-ink md:p-7">
          <b>Совет:</b> воспользуйтесь нашим <Link to="/" className="font-bold text-brand-blue hover:underline">подбором займа</Link> — одна заявка отправится сразу в 5 МФО с высоким процентом одобрения. Шансы получить деньги за один заход — близки к 100%.
        </div>
      </div>
    </section>
  );
}

function RelatedSituationsSection() {
  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Похожие подборки
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Возможно, вам подойдёт одна из этих специализированных подборок МФО.
        </p>

        <div className="mt-8 flex flex-wrap gap-3">
          {relatedSituations.map((s) => (
            <Link
              key={s.slug}
              to="/situations/$slug"
              params={{ slug: s.slug }}
              className="group inline-flex items-center gap-2 rounded-pill border border-brand-line bg-white px-5 py-3 text-sm font-extrabold text-brand-ink shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-green hover:text-brand-green"
            >
              {s.name}
              <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" strokeWidth={2.5} />
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

function SeoTextSection() {
  return (
    <section className="px-6 py-20">
      <article className="mx-auto max-w-4xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Займы без отказа — что важно знать
        </h2>
        <p className="mt-5 text-base leading-relaxed text-brand-muted md:text-lg">
          Понятие «займ без отказа» в строгом юридическом смысле не существует — закон запрещает МФО гарантировать 100% одобрение. Но на практике есть компании, чей процент положительных решений приближается к этому показателю. В нашей подборке — 24 такие организации с одобрением от 90% и выше.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Кому подходят такие займы</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Подборка создана для людей, которым уже отказывали в банках или классических МФО. Это могут быть заёмщики с просрочками в прошлом, пенсионеры, студенты, фрилансеры без официальной зарплаты или те, кто обращается за займом впервые и не имеет кредитной истории вообще.
        </p>
        <ul className="mt-4 space-y-2 text-brand-muted">
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Возраст от 18 лет (некоторые МФО — от 21).</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Гражданство РФ и действующий паспорт.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Активная банковская карта на ваше имя.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Любой источник дохода — даже неофициальный.</span></li>
        </ul>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Как работает «безотказное» одобрение</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          МФО из подборки используют упрощённый скоринг: меньше параметров, мягче критерии, нет обязательного запроса в БКИ при первом обращении. Для них важнее текущая платёжеспособность, чем прошлые ошибки. Если у вас сейчас есть стабильный доход — займ почти наверняка одобрят, даже если в прошлом были просрочки.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Стоит ли переплачивать за высокий шанс одобрения</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Распространённый миф — что МФО «без отказа» завышают ставки. На самом деле большинство компаний из подборки работают по обычным рыночным условиям: 0,8–1% в день, а первый займ часто выдают под 0%. Лояльный скоринг — это про автоматизацию, а не про переплату.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Как не попасть к мошенникам</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Если компания обещает 100% одобрение и просит «комиссию за гарантию» или предоплату — это мошенники. Все МФО из нашего <Link to="/mfo" className="font-bold text-brand-blue hover:underline">каталога</Link> проверены: имеют действующую лицензию ЦБ РФ, состоят в реестре и работают строго по закону № 151-ФЗ. Никаких авансов и комиссий за подачу заявки.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Сколько раз можно подавать заявки</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Хоть в каждую МФО из подборки одновременно. Закон не ограничивает количество одновременных заявок. Более того — параллельная подача в 3–5 компаний значительно повышает шансы на быстрое одобрение хотя бы в одной из них. Используйте наш сервис подбора, чтобы не заполнять анкеты вручную.
        </p>

        <p className="mt-10 rounded-2xl border border-[#fecaca] bg-[#fef2f2] p-6 leading-relaxed text-[#7f1d1d]">
          <b>Важно:</b> «займ без отказа» — это не повод брать деньги, которые вы не сможете вернуть. Реалистично оценивайте свои возможности и берите столько, сколько точно сможете погасить в срок. Просрочка испортит КИ и закроет доступ даже к лояльным МФО.
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
          Частые вопросы про займы без отказа
        </h2>
        <p className="mt-3 text-base text-brand-muted md:text-lg">
          Ответы на самые волнующие вопросы заёмщиков.
        </p>

        <div className="mt-8 space-y-3">
          {situationFaqs.map((f, i) => {
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
            Полная навигация по сайту
          </h2>
          <p className="mt-3 text-base text-white/70 md:text-lg">
            Подборки МФО по сумме, городу и жизненной ситуации.
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
