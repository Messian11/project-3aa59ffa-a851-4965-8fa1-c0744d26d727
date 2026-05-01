import { createFileRoute, Link } from "@tanstack/react-router";
import { useState } from "react";
import {
  ArrowRight,
  ChevronRight,
  Star,
  Building2,
  Users,
  Briefcase,
  Wallet,
  Zap,
  CreditCard,
  MapPin,
  Plus,
  Minus,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { cn } from "@/lib/utils";

export const Route = createFileRoute("/goroda/$slug")({
  head: () => ({
    meta: [
      { title: "Займ онлайн в Москве — 47 МФО на карту 2026 | Zaymi Online" },
      {
        name: "description",
        content:
          "Займ онлайн в Москве на карту любого банка. 47 МФО с лицензией ЦБ РФ, одобрение 95%, деньги за 5 минут. Сравните условия для жителей Москвы и Подмосковья.",
      },
      { property: "og:title", content: "Займ онлайн в Москве — 47 МФО 2026" },
      {
        property: "og:description",
        content: "47 МФО для жителей Москвы и МО. Деньги на карту за 5 минут, без справок.",
      },
    ],
  }),
  component: CityPage,
});

const popularCities = [
  { name: "Санкт-Петербург", slug: "sankt-peterburg" },
  { name: "Казань", slug: "kazan" },
  { name: "Новосибирск", slug: "novosibirsk" },
  { name: "Екатеринбург", slug: "ekaterinburg" },
  { name: "Нижний Новгород", slug: "nizhniy-novgorod" },
  { name: "Самара", slug: "samara" },
  { name: "Уфа", slug: "ufa" },
  { name: "Челябинск", slug: "chelyabinsk" },
];

const cityMfos = [
  { name: "Займер", slug: "zaymer", letter: "З", bg: "from-brand-blue to-brand-green", rating: 4.8, reviews: 2384, amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "95%", badges: [{ l: "Первый 0%", t: "amber" as const }, { l: "Москва ✓", t: "blue" as const }] },
  { name: "Webbankir", slug: "webbankir", letter: "W", bg: "from-brand-amber to-brand-green", rating: 4.7, reviews: 4128, amount: "до 100 000 ₽", term: "до 168 дней", rate: "от 0%", approval: "92%", badges: [{ l: "Первый 0%", t: "amber" as const }] },
  { name: "МигКредит", slug: "migcredit", letter: "М", bg: "from-brand-blue to-brand-blue/60", rating: 4.6, reviews: 1873, amount: "до 50 000 ₽", term: "до 168 дней", rate: "от 0,8%", approval: "88%", badges: [{ l: "На карту 24/7", t: "green" as const }] },
  { name: "MoneyMan", slug: "moneyman", letter: "M", bg: "from-brand-blue to-brand-amber", rating: 4.6, reviews: 3502, amount: "до 80 000 ₽", term: "до 126 дней", rate: "от 0%", approval: "91%", badges: [{ l: "Москва ✓", t: "blue" as const }] },
  { name: "Лайм-Займ", slug: "lime-zaim", letter: "Л", bg: "from-brand-green to-brand-green/60", rating: 4.5, reviews: 1564, amount: "до 70 000 ₽", term: "до 168 дней", rate: "от 1%", approval: "90%", badges: [{ l: "Без отказа", t: "green" as const }] },
  { name: "Турбозайм", slug: "turbozaym", letter: "Т", bg: "from-brand-green to-brand-blue", rating: 4.5, reviews: 2244, amount: "до 15 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "93%", badges: [{ l: "Срочно 5 мин", t: "amber" as const }] },
  { name: "VIVA Деньги", slug: "viva", letter: "V", bg: "from-brand-blue to-brand-amber", rating: 4.3, reviews: 3128, amount: "до 30 000 ₽", term: "до 60 дней", rate: "от 0,9%", approval: "87%", badges: [{ l: "С плохой КИ", t: "green" as const }] },
  { name: "Smart Credit", slug: "smart-credit", letter: "S", bg: "from-brand-amber to-brand-blue", rating: 4.3, reviews: 894, amount: "до 100 000 ₽", term: "до 365 дней", rate: "от 0,7%", approval: "84%", badges: [{ l: "Без справок", t: "green" as const }] },
];

const cityFeatures = [
  {
    icon: Building2,
    title: "Региональные МФО",
    desc: "5 московских МФО работают только с жителями региона — у них самые лояльные условия одобрения и расширенные лимиты.",
    accent: "blue" as const,
  },
  {
    icon: Zap,
    title: "Скорость одобрения",
    desc: "В Москве МФО одобряют заявки в среднем за 3 минуты. Деньги поступают на карту мгновенно — 24/7, включая выходные.",
    accent: "amber" as const,
  },
  {
    icon: CreditCard,
    title: "Карты московских банков",
    desc: "Все МФО выдают на карты МКБ, Открытие, Сбера, Тинькофф, ВТБ и Альфы. Поддерживаются также Мир, Visa и Mastercard.",
    accent: "green" as const,
  },
];

const nearbyCities = [
  { name: "Подольск", pop: "308 тыс", slug: "podolsk" },
  { name: "Химки", pop: "259 тыс", slug: "khimki" },
  { name: "Балашиха", pop: "517 тыс", slug: "balashikha" },
  { name: "Мытищи", pop: "262 тыс", slug: "mytishchi" },
  { name: "Люберцы", pop: "211 тыс", slug: "lyubertsy" },
  { name: "Красногорск", pop: "176 тыс", slug: "krasnogorsk" },
];

const cityFaqs = [
  { q: "Можно ли получить займ в Москве без прописки?", a: "Да. Большинство МФО Москвы выдают займы по паспорту независимо от штампа о регистрации — достаточно временной регистрации или справки о фактическом проживании в любом регионе РФ." },
  { q: "Какие московские банки подходят для зачисления?", a: "Карты любых банков с лицензией ЦБ РФ: Сбер, ВТБ, Тинькофф, Альфа-Банк, МКБ, Открытие, Газпромбанк, Райффайзен, Совкомбанк. Карта должна быть дебетовой и оформлена на ваше имя." },
  { q: "Работают ли МФО ночью и в выходные в Москве?", a: "Да, 100% онлайн-МФО работают круглосуточно. Заявку можно подать ночью или в воскресенье — деньги поступят на карту в течение 5–15 минут после автоматического одобрения." },
  { q: "Можно ли получить наличные в офисе МФО в Москве?", a: "Да, 12 МФО из нашего каталога имеют офисы в Москве — большинство в пределах ТТК. Адреса и графики работы указаны в карточке каждой организации." },
  { q: "Действуют ли московские акции для жителей области?", a: "Да, акция «Первый займ под 0%» доступна всем жителям РФ, включая Подмосковье. Никаких региональных ограничений по акциям у федеральных МФО нет." },
  { q: "Что делать, если МФО отказала в Москве?", a: "Подайте заявку в 2–3 другие компании параллельно — алгоритмы скоринга у всех разные. Используйте наш подбор займа: одна заявка отправляется сразу в несколько МФО, работающих с москвичами." },
];

/* ───────────── Page ───────────── */

function CityPage() {
  return (
    <div className="min-h-screen scroll-smooth bg-white">
      <SiteHeader />

      {/* 1. Breadcrumbs */}
      <nav className="border-b border-brand-line/60 bg-white px-6 py-3" aria-label="Хлебные крошки">
        <ol className="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-brand-muted">
          <li><Link to="/" className="hover:text-brand-blue">Главная</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li><Link to="/mfo" className="hover:text-brand-blue">Займы по городам</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li className="font-semibold text-brand-ink">Москва</li>
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
          <div className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-green">Займ в городе</div>
          <h1 className="mt-3 max-w-3xl text-4xl font-extrabold tracking-tight text-brand-ink md:text-5xl">
            Займ онлайн в Москве
          </h1>
          <p className="mt-4 max-w-2xl text-base text-brand-muted md:text-lg">
            47 МФО которые работают с жителями Москвы и Московской области. Получите деньги на карту не выходя из дома.
          </p>

          <div className="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <Quick icon={Building2} label="Город" value="Москва" />
            <Quick icon={Users} label="Население" value="12,6 млн" />
            <Quick icon={Briefcase} label="МФО работают" value="47" highlight />
            <Quick icon={Wallet} label="Средняя сумма" value="18 500 ₽" />
          </div>
        </div>
      </section>

      {/* 3. Popular cities row */}
      <section className="px-6 py-10">
        <div className="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center">
          <span className="text-sm font-bold text-brand-muted">Другие крупные города:</span>
          <div className="flex flex-wrap items-center gap-2">
            {popularCities.map((c) => (
              <Link
                key={c.slug}
                to="/goroda/$slug"
                params={{ slug: c.slug }}
                className="rounded-pill border border-brand-line bg-white px-4 py-2 text-sm font-bold text-brand-ink shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-blue hover:text-brand-blue"
              >
                {c.name}
              </Link>
            ))}
            <Link
              to="/mfo"
              className="rounded-pill bg-brand-ink px-4 py-2 text-sm font-bold text-white shadow-card transition-all hover:-translate-y-0.5 hover:bg-brand-ink/90"
            >
              Все города →
            </Link>
          </div>
        </div>
      </section>

      {/* 4. Catalog */}
      <CityCatalog />

      {/* 5. City-specific info */}
      <CityFeaturesSection />

      {/* 6. Nearby cities */}
      <NearbySection />

      {/* 7. SEO text */}
      <SeoTextSection />

      {/* 8. FAQ */}
      <FaqSection />

      {/* 9. Related hub */}
      <RelatedHub />

      <SiteFooter />
    </div>
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

function CityCatalog() {
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <div className="flex items-end justify-between gap-4">
          <div>
            <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
              МФО для жителей Москвы
            </h2>
            <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
              Все компании выдают займы онлайн с зачислением на карту любого банка.
            </p>
          </div>
          <Link to="/mfo" className="hidden text-sm font-bold text-brand-blue hover:underline md:inline">
            Все 47 МФО →
          </Link>
        </div>

        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {cityMfos.map((m) => (
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
                <Row k="Ставка" v={m.rate} highlight />
                <Row k="Одобрение" v={m.approval} />
              </dl>

              <button className="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
                Получить займ <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
              </button>
            </article>
          ))}
        </div>

        <div className="mt-10 text-center md:hidden">
          <Link to="/mfo" className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-6 text-sm font-bold text-brand-ink shadow-card hover:border-brand-blue hover:text-brand-blue">
            Все 47 МФО Москвы <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </Link>
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

function CityFeaturesSection() {
  return (
    <section className="bg-brand-soft px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Особенности займов в Москве
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Что отличает столичный рынок микрофинансирования.
        </p>

        <div className="mt-10 grid gap-5 md:grid-cols-3">
          {cityFeatures.map((f) => (
            <div key={f.title} className="rounded-2xl border border-brand-line bg-white p-6 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover md:p-8">
              <div
                className={cn(
                  "flex h-14 w-14 items-center justify-center rounded-2xl ring-1 ring-inset",
                  f.accent === "blue" && "bg-brand-blue/10 text-brand-blue ring-brand-blue/20",
                  f.accent === "amber" && "bg-brand-amber/15 text-[#9a6300] ring-brand-amber/30",
                  f.accent === "green" && "bg-brand-green/12 text-brand-green ring-brand-green/20",
                )}
              >
                <f.icon className="h-7 w-7" />
              </div>
              <h3 className="mt-5 text-xl font-extrabold text-brand-ink">{f.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-brand-muted">{f.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function NearbySection() {
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-7xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Жителям соседних регионов
        </h2>
        <p className="mt-3 max-w-2xl text-base text-brand-muted md:text-lg">
          Те же 47 МФО работают с жителями городов Подмосковья.
        </p>

        <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {nearbyCities.map((c) => (
            <Link
              key={c.slug}
              to="/goroda/$slug"
              params={{ slug: c.slug }}
              className="group flex items-center justify-between gap-4 rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-1 hover:border-brand-blue hover:shadow-hover"
            >
              <div className="flex items-center gap-4">
                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-blue/10 text-brand-blue">
                  <MapPin className="h-5 w-5" />
                </div>
                <div>
                  <div className="text-base font-extrabold text-brand-ink group-hover:text-brand-blue">{c.name}</div>
                  <div className="text-xs font-medium text-brand-muted">Население {c.pop}</div>
                </div>
              </div>
              <ArrowRight className="h-5 w-5 text-brand-muted transition-all group-hover:translate-x-1 group-hover:text-brand-blue" strokeWidth={2.5} />
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

function SeoTextSection() {
  return (
    <section className="bg-brand-soft px-6 py-20">
      <article className="mx-auto max-w-4xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Займы в Москве — особенности и советы
        </h2>
        <p className="mt-5 text-base leading-relaxed text-brand-muted md:text-lg">
          Москва — крупнейший рынок микрофинансирования в России. По данным ЦБ РФ за 2025 год, на столицу и Московскую область приходится около 28% всех онлайн-займов. Здесь работают как федеральные МФО, так и небольшие региональные компании, ориентированные именно на жителей мегаполиса.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Сколько МФО работают в Москве</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          В нашем каталоге собраны 47 микрофинансовых организаций, выдающих займы жителям Москвы и Московской области. Все они имеют действующую лицензию Центробанка и состоят в государственном реестре. Из них 5 — это компании с московской пропиской, у которых местные клиенты получают приоритетные условия и расширенные лимиты.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Почему москвичам одобряют чаще</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Средний доход в столице выше общероссийского, поэтому скоринговые модели МФО оценивают жителей Москвы как менее рискованных заёмщиков. Процент одобрения по московским заявкам составляет 95% против 88% в среднем по стране. Средний размер займа в Москве — 18 500 ₽, что на 35% выше федерального показателя.
        </p>
        <ul className="mt-4 space-y-2 text-brand-muted">
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Скоринг учитывает ваш регион проживания и автоматически повышает лимит для столичных клиентов.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Москвичи чаще получают суммы 30 000 ₽ и выше при первом обращении.</span></li>
          <li className="flex gap-2"><span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" /><span>Среднее время одобрения в Москве — 3 минуты, в регионах — 5–7 минут.</span></li>
        </ul>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Как получить займ в Москве онлайн</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Процедура полностью дистанционная — выезд в офис не требуется. Заполните анкету на сайте выбранной МФО, укажите паспортные данные, номер карты и сумму. Решение приходит в течение 5 минут, деньги поступают на карту мгновенно. Если хотите получить наличные — 12 МФО имеют офисы в Москве (большинство в пределах ТТК и у станций метро Курская, Тверская, Маяковская).
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">На какие карты приходят деньги в Москве</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Поддерживаются карты всех российских банков: Сбербанк, ВТБ, Тинькофф, Альфа-Банк, Газпромбанк, Открытие, МКБ, Райффайзен, Совкомбанк, Россельхозбанк. Платёжные системы — Мир, Visa, Mastercard. Карта должна быть дебетовой и оформленной на ваше имя — на кредитные карты МФО деньги не зачисляют.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Региональные акции и бонусы</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Помимо стандартной федеральной акции «Первый займ под 0%», некоторые московские МФО предлагают локальные бонусы: повышенный лимит для держателей карт МКБ, скидку на ставку при оплате через СБП, кешбэк при пролонгации. Условия меняются ежемесячно — следите за обновлениями в карточках МФО на нашем <Link to="/mfo" className="font-bold text-brand-blue hover:underline">каталоге</Link>.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Москва и Московская область — есть ли разница</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          С точки зрения МФО — нет. Жители Подольска, Балашихи, Химок и других подмосковных городов получают точно такие же условия, как москвичи. Прописка не влияет на одобрение: важен только сам факт регистрации на территории РФ. Если вы переехали в Москву из другого региона — всё равно сможете получить займ по временной регистрации или просто по фактическому адресу проживания.
        </p>

        <h3 className="mt-10 text-2xl font-extrabold text-brand-ink">Что делать при просрочке в Москве</h3>
        <p className="mt-4 leading-relaxed text-brand-muted">
          Если не можете вовремя вернуть займ — не прячьтесь, а сразу свяжитесь с МФО. В Москве работают офисы поддержки большинства компаний, где можно лично оформить пролонгацию или реструктуризацию. По закону № 230-ФЗ коллекторы не имеют права звонить вам чаще 8 раз в неделю и приходить домой без согласования.
        </p>

        <p className="mt-10 rounded-2xl border border-brand-line bg-white p-6 leading-relaxed text-brand-ink">
          <b>Вывод:</b> Москва — самый удобный регион для получения онлайн-займа. Высокая конкуренция между 47 МФО гарантирует лучшие ставки, скорость и качество сервиса. Используйте наш каталог, чтобы выбрать оптимальное предложение под вашу ситуацию.
        </p>
      </article>
    </section>
  );
}

function FaqSection() {
  const [open, setOpen] = useState<number | null>(0);
  return (
    <section className="px-6 py-16 md:py-20">
      <div className="mx-auto max-w-3xl">
        <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
          Частые вопросы про займы в Москве
        </h2>
        <p className="mt-3 text-base text-brand-muted md:text-lg">
          Ответы на вопросы, которые чаще всего задают москвичи.
        </p>

        <div className="mt-8 space-y-3">
          {cityFaqs.map((f, i) => {
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
  { title: "По городам", links: ["МФО в СПб", "МФО в Казани", "МФО в Новосибирске", "МФО в Екатеринбурге", "МФО в Самаре", "Все города"] },
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
