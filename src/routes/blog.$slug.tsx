import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import {
  ArrowRight,
  ChevronRight,
  ChevronDown,
  Star,
  Clock,
  Calendar,
  AlertTriangle,
  Lightbulb,
  ExternalLink,
  CheckCircle2,
  XCircle,
  ShieldCheck,
  TrendingUp,
  Wallet,
  ListChecks,
  Send,
  Plus,
  Minus,
  Twitter,
  Linkedin,
  Mail,
  Eye,
  Share2,
  Bookmark,
  BadgeCheck,
  Sparkles,
  Trophy,
} from "lucide-react";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { cn } from "@/lib/utils";
import blogHero from "@/assets/blog-hero.jpg";
import blogInline from "@/assets/blog-inline.jpg";
import authorAnna from "@/assets/author-anna.jpg";

export const Route = createFileRoute("/blog/$slug")({
  head: () => ({
    meta: [
      { title: "Как выбрать МФО — пошаговый гид 2026 | Zaymi Online" },
      {
        name: "description",
        content:
          "Подробный гид по выбору микрофинансовой организации в 2026 году: на что смотреть, как сравнить условия, частые ошибки и ТОП-5 проверенных МФО.",
      },
      { property: "og:title", content: "Как выбрать МФО — пошаговый гид 2026" },
      { property: "og:description", content: "Эксперты Zaymi Online — что важно знать перед оформлением онлайн-займа." },
      { property: "og:image", content: blogHero },
      { property: "og:type", content: "article" },
    ],
  }),
  component: BlogArticle,
});

const toc = [
  { id: "chto-takoe-mfo", title: "Что такое МФО" },
  { id: "mfo-vs-banki", title: "Чем МФО отличаются от банков" },
  { id: "kriterii-vybora", title: "На что обращать внимание при выборе" },
  { id: "top-mfo", title: "ТОП-5 МФО на 2026 год" },
  { id: "oshibki", title: "Частые ошибки при выборе МФО" },
  { id: "zaklyuchenie", title: "Заключение" },
];

const criteria = [
  { icon: ShieldCheck, title: "Лицензия ЦБ РФ", desc: "Проверьте номер регистрации в государственном реестре микрофинансовых организаций — без этого компания не имеет права выдавать займы." },
  { icon: TrendingUp, title: "Процентная ставка", desc: "По закону максимум 0,8% в день. Если видите больше — это нарушение, и договор можно оспорить." },
  { icon: Wallet, title: "Полная стоимость кредита", desc: "ПСК указывается в правом верхнем углу первой страницы договора. Сравнивайте именно её, а не только дневную ставку." },
  { icon: ListChecks, title: "Прозрачность условий", desc: "Никаких скрытых комиссий. Все платежи должны быть подробно расписаны: тело долга, проценты, штрафы за просрочку." },
  { icon: CheckCircle2, title: "Способы погашения", desc: "Карта, СБП, банковский перевод, терминалы. Чем больше способов — тем удобнее вернуть деньги в срок без комиссий." },
  { icon: Send, title: "Поддержка клиентов", desc: "Круглосуточный чат, телефон, email. Проверьте отзывы — насколько быстро реагируют и решают проблемы." },
  { icon: Star, title: "Реальные отзывы", desc: "Читайте на сторонних площадках: Banki.ru, Sravni.ru, Trustpilot. Внутренние отзывы на сайте МФО почти всегда модерированы." },
];

const topMfos = [
  { name: "Займер", slug: "zaymer", letter: "З", bg: "from-brand-blue to-brand-green", rating: 4.8, line: "Лучший для первого займа под 0%", amount: "до 30 000 ₽", rate: "от 0%" },
  { name: "Webbankir", slug: "webbankir", letter: "W", bg: "from-brand-amber to-brand-green", rating: 4.7, line: "Большие суммы и долгий срок", amount: "до 100 000 ₽", rate: "от 0%" },
  { name: "MoneyMan", slug: "moneyman", letter: "M", bg: "from-brand-blue to-brand-amber", rating: 4.6, line: "Удобное мобильное приложение", amount: "до 80 000 ₽", rate: "от 0%" },
  { name: "Лайм-Займ", slug: "lime-zaim", letter: "Л", bg: "from-brand-green to-brand-green/60", rating: 4.5, line: "Лояльны к плохой кредитной истории", amount: "до 70 000 ₽", rate: "от 1%" },
  { name: "МигКредит", slug: "migcredit", letter: "М", bg: "from-brand-blue to-brand-blue/60", rating: 4.6, line: "Стабильное одобрение и хороший сервис", amount: "до 50 000 ₽", rate: "от 0,8%" },
];

const rankStyles = [
  "bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500 text-white shadow-[0_8px_20px_-6px_rgba(251,146,60,0.6)]",
  "bg-gradient-to-br from-slate-300 via-slate-400 to-slate-500 text-white shadow-[0_8px_20px_-6px_rgba(100,116,139,0.5)]",
  "bg-gradient-to-br from-orange-700 via-orange-800 to-amber-900 text-white shadow-[0_8px_20px_-6px_rgba(154,52,18,0.5)]",
  "bg-brand-soft text-brand-ink ring-1 ring-inset ring-brand-line",
  "bg-brand-soft text-brand-ink ring-1 ring-inset ring-brand-line",
];

const mistakes = [
  { title: "Брать первое попавшееся предложение", desc: "Разница в переплате между МФО на одну и ту же сумму может достигать 40%. Всегда сравнивайте минимум 3 варианта." },
  { title: "Не читать договор", desc: "Особенно мелкий шрифт. Именно там прячутся условия пролонгации, штрафы и автоплатежи, которые потом удивят." },
  { title: "Использовать займ для погашения другого", desc: "Прямой путь к долговой яме. Если не справляетесь — лучше договориться о реструктуризации, чем брать новый займ." },
  { title: "Игнорировать дату возврата", desc: "Поставьте напоминание за 2 дня до срока. Один день просрочки — и ставка пересчитывается на полную, без льготных условий." },
  { title: "Доверять обещаниям «100% одобрения»", desc: "По закону такое запрещено. Если компания гарантирует выдачу — скорее всего, перед вами мошенники, которые попросят «комиссию»." },
];

const relatedArticles = [
  { slug: "kak-povysit-shansy-na-odobrenie", title: "Как повысить шансы на одобрение займа", excerpt: "10 проверенных способов поднять кредитный рейтинг и получить деньги без отказа.", date: "22 апр", readTime: "6 мин", category: "Советы", bg: "from-brand-blue to-brand-green" },
  { slug: "chto-takoe-psk", title: "Что такое ПСК и как её рассчитать", excerpt: "Разбираем полную стоимость кредита на простых примерах с реальными цифрами.", date: "18 апр", readTime: "5 мин", category: "Финграмотность", bg: "from-brand-amber to-brand-green" },
  { slug: "moshenniki-pod-vidom-mfo", title: "Как распознать мошенников под видом МФО", excerpt: "7 явных признаков, что перед вами не легальная компания, а аферисты.", date: "12 апр", readTime: "7 мин", category: "Безопасность", bg: "from-brand-blue to-brand-amber" },
];

const articleFaqs = [
  { q: "Какая МФО самая лучшая в 2026 году?", a: "Универсального ответа нет — всё зависит от ваших задач. Для первого займа под 0% лучше Займер, для крупной суммы — Webbankir, для лояльной оценки КИ — Лайм-Займ. Используйте наш каталог с фильтрами." },
  { q: "Можно ли взять займ в нескольких МФО одновременно?", a: "Юридически — да, ограничений нет. Но это рискованно: проще запутаться в датах возврата и попасть в просрочку. Лучше брать в одной компании сумму побольше." },
  { q: "Как понять, что МФО не мошенники?", a: "Проверьте номер в реестре ЦБ РФ (cbr.ru/microfinance), наличие реквизитов в подвале сайта, защищённое HTTPS-соединение. Никогда не платите «за одобрение» — это незаконно." },
  { q: "Что делать, если просрочил выплату?", a: "Сразу свяжитесь с МФО — большинство предложат пролонгацию или реструктуризацию. Не прячьтесь: проблема не решится сама, а штрафы будут расти." },
];

/* ───────────── Page ───────────── */

function BlogArticle() {
  return (
    <div className="min-h-screen scroll-smooth bg-white">
      <SiteHeader />
      <ReadingProgress />

      {/* 1. Breadcrumbs */}
      <nav className="border-b border-brand-line/60 bg-white px-5 py-3 md:px-6" aria-label="Хлебные крошки">
        <ol className="mx-auto flex max-w-7xl items-center gap-1.5 text-xs font-medium text-brand-muted">
          <li><Link to="/" className="hover:text-brand-blue">Главная</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li><Link to="/blog" className="hover:text-brand-blue">Блог</Link></li>
          <ChevronRight className="h-3.5 w-3.5 text-brand-line" />
          <li className="truncate font-semibold text-brand-ink">Как выбрать МФО</li>
        </ol>
      </nav>

      {/* 2. Article hero */}
      <header className="relative overflow-hidden px-5 pt-12 pb-10 md:px-6 md:pt-20">
        {/* decorative blobs */}
        <div className="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-brand-blue/15 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-32 -left-24 h-72 w-72 rounded-full bg-brand-green/15 blur-3xl" />

        <div className="relative mx-auto max-w-3xl text-center">
          <div className="flex flex-wrap items-center justify-center gap-2">
            <span className="inline-flex items-center gap-1.5 rounded-pill bg-brand-blue/10 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-brand-blue ring-1 ring-inset ring-brand-blue/20">
              <Sparkles className="h-3 w-3" /> Гид
            </span>
            <span className="inline-flex items-center gap-1.5 rounded-pill bg-brand-green/10 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-brand-green ring-1 ring-inset ring-brand-green/20">
              Обновлено • 2026
            </span>
          </div>
          <h1 className="mt-5 text-3xl font-extrabold leading-[1.1] tracking-tight text-brand-ink sm:text-4xl md:text-5xl lg:text-6xl">
            Как выбрать МФО —<br className="hidden sm:block" /> пошаговый гид 2026
          </h1>
          <p className="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-brand-muted md:text-xl">
            Полный разбор от финансового эксперта: на что смотреть, как сравнивать условия и какие 5 критериев действительно влияют на выгодность займа.
          </p>

          {/* author + meta pill */}
          <div className="mx-auto mt-8 inline-flex max-w-full flex-wrap items-center justify-center gap-3 rounded-2xl border border-brand-line bg-white/80 px-4 py-3 shadow-card backdrop-blur-sm sm:gap-5 sm:px-5">
            <div className="flex items-center gap-3">
              <img src={authorAnna} alt="Анна Петрова" width={44} height={44} loading="lazy" className="h-11 w-11 rounded-full object-cover ring-2 ring-brand-green/30" />
              <div className="text-left">
                <div className="flex items-center gap-1 text-sm font-extrabold text-brand-ink">
                  Анна Петрова <BadgeCheck className="h-4 w-4 text-brand-blue" />
                </div>
                <div className="text-[11px] font-semibold text-brand-muted">Финансовый эксперт</div>
              </div>
            </div>
            <span className="hidden h-8 w-px bg-brand-line sm:block" />
            <div className="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs font-semibold text-brand-muted">
              <span className="inline-flex items-center gap-1"><Calendar className="h-3.5 w-3.5" /> 29 апр 2026</span>
              <span className="inline-flex items-center gap-1"><Clock className="h-3.5 w-3.5" /> 8 мин</span>
              <span className="inline-flex items-center gap-1"><Eye className="h-3.5 w-3.5" /> 12.4k</span>
            </div>
          </div>
        </div>
      </header>

      {/* 3. Featured image */}
      <figure className="px-5 md:px-6">
        <div className="relative mx-auto max-w-5xl">
          <img
            src={blogHero}
            alt="Финансовый эксперт сравнивает предложения МФО"
            width={1600}
            height={896}
            className="aspect-[16/9] w-full rounded-2xl object-cover shadow-hover md:rounded-3xl"
          />
          <div className="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-pill bg-white/95 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-wider text-brand-ink shadow-card backdrop-blur md:left-6 md:top-6">
            <Trophy className="h-3.5 w-3.5 text-brand-amber" /> Гид №1 в апреле
          </div>
          <figcaption className="mt-3 text-center text-xs italic text-brand-muted md:text-sm">
            Правильный выбор МФО — это про сравнение и внимательное чтение договора, а не про скорость клика.
          </figcaption>
        </div>
      </figure>

      {/* 4 + 5. TOC + Article */}
      <div className="px-5 py-12 md:px-6 md:py-20">
        <div className="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[260px_1fr] lg:gap-12">
          <aside>
            <TocPanel />
          </aside>

          <article className="prose-article min-w-0">
            <Lead>
              По данным Центробанка, в России работает более 1 000 микрофинансовых организаций. Большинство легальны и работают по закону, но условия у них отличаются в разы. В этом гиде мы разберём, как выбрать МФО, в которой заём будет выгодным, а сервис — без сюрпризов.
            </Lead>

            {/* Section 1 */}
            <H2 id="chto-takoe-mfo">Что такое МФО</H2>
            <P>
              МФО — это микрофинансовая организация, которая выдаёт небольшие краткосрочные займы. В отличие от банков, МФО не привлекают вклады населения и работают по упрощённой модели: меньше документов, выше скорость, но и более высокие ставки.
            </P>
            <P>
              Все легальные МФО в России включены в государственный реестр Центробанка. Это публичный список, его можно бесплатно проверить на сайте <ExtLink href="https://cbr.ru/microfinance/">cbr.ru/microfinance</ExtLink>. Если компании там нет — она работает незаконно, и связываться с ней опасно.
            </P>
            <P>
              По закону № 151-ФЗ микрофинансовые организации делятся на два типа: МФК (могут выдавать до 1 млн ₽) и МКК (до 500 тыс. ₽). Большинство онлайн-МФО — это МКК, специализирующиеся на займах «до зарплаты».
            </P>

            <Quote author="Юрий Лужин, эксперт ЦБ РФ">
              «Главное правило заёмщика — никогда не брать в МФО, которой нет в реестре. Это первый и самый надёжный фильтр против мошенников».
            </Quote>

            {/* Section 2 */}
            <H2 id="mfo-vs-banki">Чем МФО отличаются от банков</H2>

            <figure className="my-8">
              <img
                src={blogInline}
                alt="Сравнение МФО и банков"
                width={1280}
                height={720}
                loading="lazy"
                className="aspect-[16/9] w-full rounded-2xl object-cover shadow-card"
              />
              <figcaption className="mt-3 text-center text-xs italic text-brand-muted md:text-sm">
                МФО выигрывает в скорости, банк — в стоимости. Выбор зависит от вашей ситуации.
              </figcaption>
            </figure>

            <CompareTable />

            <P>
              Главный плюс МФО — скорость: заявка обрабатывается за 5 минут, деньги приходят на карту мгновенно. Банк же может проверять документы 1–3 дня, и без официальной зарплаты, скорее всего, откажет.
            </P>
            <P>
              Главный минус — стоимость. Дневная ставка в МФО в среднем 0,8% (это ~292% годовых). Поэтому МФО — не про долгий кредит, а про закрытие срочной финансовой дыры на 5–30 дней.
            </P>

            <ImportantBox>
              Если вам нужны деньги на срок более 3–4 месяцев и есть стабильная зарплата — рассмотрите банковский кредит наличными. На длинной дистанции переплата будет в 5–10 раз ниже, чем у МФО.
            </ImportantBox>

            {/* Section 3 */}
            <H2 id="kriterii-vybora">На что обращать внимание при выборе МФО</H2>
            <P>
              Это самая важная часть гида. Проверьте каждое выбранное предложение по этим 7 пунктам — и вы избежите 95% типичных ошибок заёмщиков.
            </P>

            <ol className="not-prose mt-8 space-y-3 list-none pl-0">
              {criteria.map((c, i) => (
                <li
                  key={c.title}
                  className="group relative flex gap-4 overflow-hidden rounded-2xl border border-brand-line bg-white p-4 shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-green/40 hover:shadow-hover sm:p-5"
                >
                  <div className="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-brand-green to-brand-blue opacity-0 transition-opacity group-hover:opacity-100" />
                  <div className="flex shrink-0 flex-col items-center gap-2">
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-ink text-sm font-black text-white shadow-card">
                      {String(i + 1).padStart(2, "0")}
                    </div>
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-green/15 to-brand-blue/15 text-brand-green ring-1 ring-inset ring-brand-green/20">
                      <c.icon className="h-5 w-5" strokeWidth={2.2} />
                    </div>
                  </div>
                  <div className="min-w-0 flex-1">
                    <h3 className="m-0 text-base font-extrabold text-brand-ink">{c.title}</h3>
                    <p className="mt-1 mb-0 text-sm leading-relaxed text-brand-muted">{c.desc}</p>
                  </div>
                </li>
              ))}
            </ol>

            <ProConsBox
              pros={[
                "Проверка лицензии в реестре ЦБ РФ занимает 30 секунд",
                "Сравнение ПСК помогает увидеть реальную переплату",
                "Внешние отзывы дают объективную картину сервиса",
              ]}
              cons={[
                "Сравнение по дневной ставке вводит в заблуждение",
                "Внутренние отзывы на сайте МФО почти всегда отфильтрованы",
                "Скрытые комиссии часто всплывают только при просрочке",
              ]}
            />

            <TipBox>
              Сохраните себе чек-лист из 7 критериев в заметках телефона. Перед каждым новым займом проходите по нему — это занимает 2 минуты, а экономит тысячи рублей.
            </TipBox>

            {/* Section 4 */}
            <H2 id="top-mfo">ТОП-5 МФО на 2026 год</H2>
            <P>
              На основе наших критериев и статистики реальных одобрений, вот пятёрка МФО, которым можно доверять прямо сейчас.
            </P>

            <div className="not-prose mt-8 space-y-3">
              {topMfos.map((m, i) => (
                <div
                  key={m.slug}
                  className="group relative flex flex-col gap-4 rounded-2xl border border-brand-line bg-white p-4 shadow-card transition-all hover:-translate-y-0.5 hover:border-brand-blue/40 hover:shadow-hover sm:p-5 md:grid md:grid-cols-[56px_64px_1fr_auto] md:items-center md:gap-5"
                >
                  {/* mobile row: rank + logo + rating */}
                  <div className="flex items-center gap-3 md:contents">
                    <div className={cn("flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-base font-black md:h-14 md:w-14 md:text-lg", rankStyles[i])}>
                      {i + 1}
                    </div>
                    <div className={cn("flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-extrabold text-white shadow-card", m.bg)}>
                      {m.letter}
                    </div>
                    {/* mobile-only rating, pushed right */}
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
                      </div>
                    </div>
                    <p className="mt-1 text-sm text-brand-muted">{m.line}</p>
                    <p className="mt-1 text-xs font-bold text-brand-muted">
                      {m.amount} • <span className="text-brand-green">{m.rate}</span>
                    </p>
                  </div>

                  <Link
                    to="/mfo/$slug"
                    params={{ slug: m.slug }}
                    className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-pill bg-brand-green px-5 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover md:w-auto"
                  >
                    Подробнее <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" strokeWidth={2.5} />
                  </Link>
                </div>
              ))}
            </div>

            {/* Section 5 */}
            <H2 id="oshibki">Частые ошибки при выборе МФО</H2>
            <P>
              Эти 5 ошибок встречаются чаще всего — и каждая из них может стоить вам тысячи рублей или испорченной кредитной истории.
            </P>

            <ol className="not-prose mt-8 space-y-3 list-none pl-0">
              {mistakes.map((m) => (
                <li
                  key={m.title}
                  className="relative flex gap-4 overflow-hidden rounded-2xl border border-[#fecaca] bg-gradient-to-br from-[#fef2f2] to-white p-4 shadow-card sm:p-5"
                >
                  <div className="absolute inset-y-0 left-0 w-1 bg-[#dc2626]" />
                  <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#dc2626]/10 text-[#dc2626] ring-1 ring-inset ring-[#dc2626]/20">
                    <XCircle className="h-5 w-5" strokeWidth={2.5} />
                  </div>
                  <div className="min-w-0 flex-1">
                    <h3 className="m-0 text-base font-extrabold text-[#7f1d1d]">{m.title}</h3>
                    <p className="mt-1 mb-0 text-sm leading-relaxed text-[#7f1d1d]/80">{m.desc}</p>
                  </div>
                </li>
              ))}
            </ol>

            {/* Section 6 */}
            <H2 id="zaklyuchenie">Заключение</H2>
            <P>
              Выбор МФО — это не лотерея, а понятная процедура из 5–10 минут. Проверьте лицензию, сравните ПСК у 3 компаний, прочитайте договор и обратите внимание на реальные отзывы. Этого достаточно, чтобы получить выгодный займ без сюрпризов.
            </P>
            <P>
              Помните главное: МФО — это инструмент для срочных задач на короткий срок. Не используйте его как замену зарплаты или кредитной карты. Берите ровно столько, сколько сможете вернуть в срок — и микрофинансы останутся вашим другом, а не проблемой.
            </P>

            {/* Share bar */}
            <ShareBar />
          </article>
        </div>
      </div>

      <CtaCard />
      <AuthorBio />
      <RelatedArticles />
      <FaqSection />
      <NewsletterCard />

      <SiteFooter />
    </div>
  );
}

/* ───────────── Reading progress bar ───────────── */

function ReadingProgress() {
  const [progress, setProgress] = useState(0);
  useEffect(() => {
    const onScroll = () => {
      const h = document.documentElement;
      const scrolled = h.scrollTop;
      const max = h.scrollHeight - h.clientHeight;
      setProgress(max > 0 ? (scrolled / max) * 100 : 0);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);
  return (
    <div className="sticky top-0 z-40 h-1 w-full bg-transparent">
      <div
        className="h-full bg-gradient-to-r from-brand-green via-brand-blue to-brand-amber transition-[width] duration-150"
        style={{ width: `${progress}%` }}
      />
    </div>
  );
}

/* ───────────── Reading components ───────────── */

function H2({ id, children }: { id: string; children: React.ReactNode }) {
  return (
    <h2 id={id} className="mt-14 mb-5 scroll-mt-28 text-2xl font-extrabold tracking-tight text-brand-ink sm:text-3xl md:text-4xl">
      {children}
    </h2>
  );
}

function P({ children }: { children: React.ReactNode }) {
  return <p className="my-5 text-[15px] leading-[1.7] text-brand-ink/85 md:text-[17px]">{children}</p>;
}

function Lead({ children }: { children: React.ReactNode }) {
  return (
    <p className="mb-8 rounded-2xl border border-brand-green/20 bg-brand-green/5 p-5 text-base italic leading-[1.7] text-brand-ink md:p-6 md:text-xl">
      {children}
    </p>
  );
}

function ExtLink({ href, children }: { href: string; children: React.ReactNode }) {
  return (
    <a href={href} target="_blank" rel="noopener noreferrer" className="font-bold text-brand-blue underline decoration-brand-blue/30 underline-offset-4 transition-colors hover:text-brand-green hover:decoration-brand-green/40">
      {children}
      <ExternalLink className="ml-0.5 inline h-3.5 w-3.5" />
    </a>
  );
}

function Quote({ author, children }: { author: string; children: React.ReactNode }) {
  return (
    <blockquote className="my-8 rounded-2xl border-l-4 border-brand-green bg-brand-green/5 p-5 md:p-6">
      <p className="m-0 text-base leading-[1.6] text-brand-ink md:text-xl">{children}</p>
      <footer className="mt-3 text-sm font-bold text-brand-muted">— {author}</footer>
    </blockquote>
  );
}

function ImportantBox({ children }: { children: React.ReactNode }) {
  return (
    <div className="my-8 flex gap-4 rounded-2xl border border-brand-amber/40 bg-brand-amber/10 p-4 md:p-5">
      <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-amber text-white">
        <AlertTriangle className="h-5 w-5" strokeWidth={2.5} />
      </div>
      <div className="min-w-0">
        <div className="text-xs font-extrabold uppercase tracking-wider text-[#9a6300]">Важно</div>
        <p className="mt-1 mb-0 text-sm leading-[1.65] text-brand-ink md:text-base">{children}</p>
      </div>
    </div>
  );
}

function TipBox({ children }: { children: React.ReactNode }) {
  return (
    <div className="my-8 flex gap-4 rounded-2xl border border-brand-green/30 bg-brand-green/[0.08] p-4 md:p-5">
      <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-green text-white">
        <Lightbulb className="h-5 w-5" strokeWidth={2.5} />
      </div>
      <div className="min-w-0">
        <div className="text-xs font-extrabold uppercase tracking-wider text-brand-green">Совет</div>
        <p className="mt-1 mb-0 text-sm leading-[1.65] text-brand-ink md:text-base">{children}</p>
      </div>
    </div>
  );
}

function ProConsBox({ pros, cons }: { pros: string[]; cons: string[] }) {
  return (
    <div className="not-prose my-8 grid gap-4 md:grid-cols-2">
      <div className="rounded-2xl border border-brand-green/30 bg-brand-green/5 p-5">
        <div className="flex items-center gap-2 text-sm font-extrabold uppercase tracking-wider text-brand-green">
          <CheckCircle2 className="h-5 w-5" /> Плюсы
        </div>
        <ul className="mt-3 space-y-2">
          {pros.map((p) => (
            <li key={p} className="flex gap-2 text-sm leading-relaxed text-brand-ink">
              <span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-green" />
              {p}
            </li>
          ))}
        </ul>
      </div>
      <div className="rounded-2xl border border-[#fecaca] bg-[#fef2f2] p-5">
        <div className="flex items-center gap-2 text-sm font-extrabold uppercase tracking-wider text-[#dc2626]">
          <XCircle className="h-5 w-5" /> Минусы
        </div>
        <ul className="mt-3 space-y-2">
          {cons.map((p) => (
            <li key={p} className="flex gap-2 text-sm leading-relaxed text-brand-ink">
              <span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#dc2626]" />
              {p}
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}

function CompareTable() {
  const rows = [
    ["Скорость одобрения", "5 минут", "1–3 дня"],
    ["Документы", "Только паспорт", "Паспорт + 2-НДФЛ"],
    ["Макс. сумма", "до 100 000 ₽", "до 5 000 000 ₽"],
    ["Срок", "до 12 мес", "до 7 лет"],
    ["Ставка", "0,8% / день", "12–25% / год"],
    ["Одобрение с плохой КИ", "Часто", "Редко"],
  ];
  return (
    <div className="not-prose my-8 -mx-5 overflow-x-auto md:mx-0">
      <div className="min-w-[480px] overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card md:min-w-0">
        <table className="w-full text-left text-sm">
          <thead className="bg-brand-ink text-white">
            <tr>
              <th className="px-4 py-4 text-xs font-extrabold uppercase tracking-wider md:px-5">Критерий</th>
              <th className="px-4 py-4 text-xs font-extrabold uppercase tracking-wider md:px-5">МФО</th>
              <th className="px-4 py-4 text-xs font-extrabold uppercase tracking-wider md:px-5">Банк</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((r, i) => (
              <tr key={i} className="border-t border-brand-line">
                <td className="px-4 py-3.5 font-bold text-brand-ink md:px-5">{r[0]}</td>
                <td className="px-4 py-3.5 font-extrabold text-brand-green md:px-5">{r[1]}</td>
                <td className="px-4 py-3.5 font-bold text-brand-ink md:px-5">{r[2]}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function ShareBar() {
  return (
    <div className="not-prose mt-12 flex flex-col gap-3 rounded-2xl border border-brand-line bg-brand-soft p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
      <div className="text-sm font-extrabold text-brand-ink">Понравилась статья? Поделитесь</div>
      <div className="flex flex-wrap items-center gap-2">
        <button className="inline-flex h-10 items-center gap-2 rounded-pill border border-brand-line bg-white px-4 text-xs font-bold text-brand-ink transition-all hover:border-brand-blue hover:text-brand-blue">
          <Twitter className="h-4 w-4" /> Twitter
        </button>
        <button className="inline-flex h-10 items-center gap-2 rounded-pill border border-brand-line bg-white px-4 text-xs font-bold text-brand-ink transition-all hover:border-brand-blue hover:text-brand-blue">
          <Linkedin className="h-4 w-4" /> LinkedIn
        </button>
        <button className="inline-flex h-10 items-center gap-2 rounded-pill border border-brand-line bg-white px-4 text-xs font-bold text-brand-ink transition-all hover:border-brand-green hover:text-brand-green">
          <Share2 className="h-4 w-4" /> Скопировать
        </button>
        <button className="inline-flex h-10 items-center gap-2 rounded-pill border border-brand-line bg-white px-4 text-xs font-bold text-brand-ink transition-all hover:border-brand-amber hover:text-brand-amber">
          <Bookmark className="h-4 w-4" /> Сохранить
        </button>
      </div>
    </div>
  );
}

/* ───────────── TOC ───────────── */

function TocPanel() {
  const [open, setOpen] = useState(false);
  const [active, setActive] = useState<string>(toc[0].id);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        const visible = entries
          .filter((e) => e.isIntersecting)
          .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible[0]) setActive(visible[0].target.id);
      },
      { rootMargin: "-30% 0px -60% 0px", threshold: 0 }
    );
    toc.forEach((i) => {
      const el = document.getElementById(i.id);
      if (el) observer.observe(el);
    });
    return () => observer.disconnect();
  }, []);

  return (
    <div className="lg:sticky lg:top-24">
      <div className="overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card">
        <button
          onClick={() => setOpen((o) => !o)}
          className="flex w-full items-center justify-between gap-2 px-5 py-4 text-left lg:cursor-default"
        >
          <span className="text-xs font-extrabold uppercase tracking-[0.16em] text-brand-muted">Содержание</span>
          <ChevronDown className={cn("h-4 w-4 text-brand-muted transition-transform lg:hidden", open && "rotate-180")} />
        </button>
        <ul className={cn("space-y-1 px-3 pb-4", open ? "block" : "hidden lg:block")}>
          {toc.map((item, i) => {
            const isActive = active === item.id;
            return (
              <li key={item.id}>
                <a
                  href={`#${item.id}`}
                  onClick={() => setOpen(false)}
                  className={cn(
                    "flex items-baseline gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition-all",
                    isActive
                      ? "bg-brand-green/10 text-brand-green ring-1 ring-inset ring-brand-green/20"
                      : "text-brand-ink/80 hover:bg-brand-soft hover:text-brand-blue"
                  )}
                >
                  <span className={cn("text-xs font-bold", isActive ? "text-brand-green" : "text-brand-muted")}>
                    {String(i + 1).padStart(2, "0")}
                  </span>
                  <span className="leading-snug">{item.title}</span>
                </a>
              </li>
            );
          })}
        </ul>
      </div>
    </div>
  );
}

/* ───────────── Bottom sections ───────────── */

function CtaCard() {
  return (
    <section className="px-5 py-8 md:px-6">
      <div className="mx-auto max-w-4xl">
        <div
          className="relative overflow-hidden rounded-3xl p-6 text-white shadow-hover sm:p-8 md:p-12"
          style={{
            background:
              "radial-gradient(circle at 80% 0%, rgba(255,255,255,0.18), transparent 50%), radial-gradient(circle at 0% 100%, rgba(255,255,255,0.12), transparent 50%), linear-gradient(135deg, #10b981 0%, #2563eb 100%)",
          }}
        >
          <div className="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl" />
          <div className="relative grid gap-6 md:grid-cols-[1fr_auto] md:items-center">
            <div>
              <span className="inline-flex items-center gap-1.5 rounded-pill bg-white/15 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider ring-1 ring-inset ring-white/20 backdrop-blur">
                <Sparkles className="h-3 w-3" /> 50+ МФО с лицензией
              </span>
              <h3 className="mt-3 text-xl font-extrabold leading-tight sm:text-2xl md:text-3xl">
                Готовы взять займ в проверенном МФО?
              </h3>
              <p className="mt-3 text-sm text-white/90 md:text-lg">
                В нашем каталоге — отсортировано по рейтингу, ставкам и проценту одобрения.
              </p>
            </div>
            <Link
              to="/mfo"
              className="inline-flex h-14 w-full items-center justify-center gap-2 rounded-pill bg-white px-7 text-base font-extrabold text-brand-ink shadow-card transition-all hover:-translate-y-0.5 hover:shadow-hover md:w-auto"
            >
              Открыть каталог МФО <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}

function AuthorBio() {
  return (
    <section className="px-5 py-12 md:px-6">
      <div className="mx-auto max-w-4xl">
        <div className="relative overflow-hidden rounded-3xl border border-brand-line bg-white p-6 shadow-card md:p-8">
          <div
            className="pointer-events-none absolute inset-x-0 top-0 h-24"
            style={{
              background:
                "linear-gradient(135deg, rgba(16,185,129,0.10) 0%, rgba(37,99,235,0.10) 100%)",
            }}
          />
          <div className="relative grid gap-5 sm:grid-cols-[100px_1fr] sm:gap-6">
            <div className="flex justify-center sm:block">
              <img
                src={authorAnna}
                alt="Анна Петрова"
                width={88}
                height={88}
                loading="lazy"
                className="h-22 w-22 rounded-full object-cover ring-4 ring-white shadow-card"
                style={{ height: 88, width: 88 }}
              />
            </div>
            <div className="text-center sm:text-left">
              <div className="inline-flex items-center gap-1 rounded-pill bg-brand-green/10 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-[0.16em] text-brand-green ring-1 ring-inset ring-brand-green/20">
                <BadgeCheck className="h-3 w-3" /> Автор
              </div>
              <h3 className="mt-2 text-xl font-extrabold text-brand-ink">Анна Петрова</h3>
              <div className="text-sm font-semibold text-brand-muted">Финансовый эксперт, 12 лет в банковской сфере</div>
              <p className="mt-3 text-sm leading-relaxed text-brand-ink/80">
                Бывший руководитель отдела розничного кредитования Альфа-Банка. Помогает читателям Zaymi Online разбираться в финансах простым языком — без воды и сложных терминов. Автор более 200 статей о займах, кредитах и личных финансах.
              </p>
              <div className="mt-4 flex items-center justify-center gap-2 sm:justify-start">
                <SocialLink icon={Twitter} />
                <SocialLink icon={Linkedin} />
                <SocialLink icon={Mail} />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function SocialLink({ icon: Icon }: { icon: typeof Twitter }) {
  return (
    <a href="#" className="flex h-10 w-10 items-center justify-center rounded-full border border-brand-line bg-white text-brand-muted transition-all hover:-translate-y-0.5 hover:border-brand-blue hover:text-brand-blue">
      <Icon className="h-4 w-4" />
    </a>
  );
}

function RelatedArticles() {
  return (
    <section className="bg-brand-soft px-5 py-14 md:px-6 md:py-20">
      <div className="mx-auto max-w-7xl">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <div>
            <h2 className="text-2xl font-extrabold tracking-tight text-brand-ink sm:text-3xl md:text-4xl">
              Читайте также
            </h2>
            <p className="mt-2 max-w-2xl text-sm text-brand-muted md:text-lg">
              Другие гиды и статьи на близкие темы.
            </p>
          </div>
          <Link to="/blog" className="inline-flex items-center gap-1.5 text-sm font-bold text-brand-blue hover:text-brand-green">
            Все статьи <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </Link>
        </div>

        <div className="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {relatedArticles.map((a) => (
            <Link
              key={a.slug}
              to="/blog/$slug"
              params={{ slug: a.slug }}
              className="group flex flex-col overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card transition-all hover:-translate-y-1 hover:shadow-hover"
            >
              <div className={cn("relative aspect-[16/9] w-full bg-gradient-to-br", a.bg)}>
                <span className="absolute left-3 top-3 inline-flex items-center rounded-pill bg-white/95 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-brand-ink shadow-card backdrop-blur">
                  {a.category}
                </span>
                <span className="absolute right-3 top-3 inline-flex items-center gap-1 rounded-pill bg-black/30 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur">
                  <Clock className="h-3 w-3" /> {a.readTime}
                </span>
              </div>
              <div className="flex flex-1 flex-col p-5">
                <div className="text-xs font-semibold text-brand-muted">{a.date}</div>
                <h3 className="mt-2 text-lg font-extrabold leading-tight text-brand-ink group-hover:text-brand-blue">
                  {a.title}
                </h3>
                <p className="mt-2 flex-1 text-sm leading-relaxed text-brand-muted">{a.excerpt}</p>
                <span className="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-green">
                  Читать <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" strokeWidth={2.5} />
                </span>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

function FaqSection() {
  const [open, setOpen] = useState<number | null>(0);
  return (
    <section className="px-5 py-14 md:px-6 md:py-20">
      <div className="mx-auto max-w-3xl">
        <h2 className="text-2xl font-extrabold tracking-tight text-brand-ink sm:text-3xl md:text-4xl">
          Частые вопросы по теме
        </h2>
        <p className="mt-2 text-sm text-brand-muted md:text-lg">
          Если что-то осталось непонятным — ответы здесь.
        </p>

        <div className="mt-8 space-y-3">
          {articleFaqs.map((f, i) => {
            const isOpen = open === i;
            return (
              <div key={i} className={cn("overflow-hidden rounded-2xl border bg-white shadow-card transition-all", isOpen ? "border-brand-green/40" : "border-brand-line")}>
                <button
                  onClick={() => setOpen(isOpen ? null : i)}
                  className="flex w-full items-center justify-between gap-4 px-4 py-4 text-left sm:px-5"
                >
                  <span className="text-sm font-extrabold text-brand-ink sm:text-base">{f.q}</span>
                  <span className={cn("flex h-8 w-8 shrink-0 items-center justify-center rounded-full transition-all", isOpen ? "bg-brand-green text-white" : "bg-brand-soft text-brand-ink")}>
                    {isOpen ? <Minus className="h-4 w-4" /> : <Plus className="h-4 w-4" />}
                  </span>
                </button>
                {isOpen && (
                  <div className="border-t border-brand-line px-4 py-4 text-sm leading-relaxed text-brand-muted sm:px-5">
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

function NewsletterCard() {
  return (
    <section className="px-5 pb-16 md:px-6">
      <div className="mx-auto max-w-4xl">
        <div className="relative overflow-hidden rounded-3xl border border-brand-line bg-brand-soft p-6 shadow-card sm:p-8 md:p-10">
          <div className="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-green/15 blur-3xl" />
          <div className="pointer-events-none absolute -left-16 -bottom-16 h-48 w-48 rounded-full bg-brand-blue/10 blur-3xl" />
          <div className="relative grid gap-5 md:grid-cols-[1fr_auto] md:items-center">
            <div>
              <div className="text-[11px] font-extrabold uppercase tracking-[0.18em] text-brand-green">Рассылка</div>
              <h3 className="mt-2 text-xl font-extrabold text-brand-ink sm:text-2xl md:text-3xl">
                Новые гиды раз в неделю — без спама
              </h3>
              <p className="mt-2 text-sm text-brand-muted md:text-base">
                Только полезные материалы про займы, финансы и кредитную историю.
              </p>
              <div className="mt-3 flex flex-wrap gap-2 text-[11px] font-bold text-brand-muted">
                <span className="inline-flex items-center gap-1 rounded-pill bg-white px-2.5 py-1 ring-1 ring-inset ring-brand-line">
                  <CheckCircle2 className="h-3 w-3 text-brand-green" /> 12 400+ читателей
                </span>
                <span className="inline-flex items-center gap-1 rounded-pill bg-white px-2.5 py-1 ring-1 ring-inset ring-brand-line">
                  <CheckCircle2 className="h-3 w-3 text-brand-green" /> Отписка в 1 клик
                </span>
              </div>
            </div>
            <form className="flex w-full flex-col gap-2 sm:flex-row md:w-auto">
              <input
                type="email"
                placeholder="ваш@email.ru"
                className="h-12 w-full min-w-0 rounded-pill border border-brand-line bg-white px-5 text-sm font-semibold text-brand-ink outline-none focus:border-brand-blue sm:min-w-[220px] md:min-w-[240px]"
              />
              <button
                type="submit"
                className="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-pill bg-brand-green px-6 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover"
              >
                Подписаться
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
}
