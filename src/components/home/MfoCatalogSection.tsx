import { useState } from "react";
import { Link } from "@tanstack/react-router";
import { ArrowRight, Star, FileText } from "lucide-react";
import { cn } from "@/lib/utils";

const filters = [
  "Все",
  "Без отказа",
  "Первый займ 0%",
  "На карту",
  "С плохой КИ",
  "На карту Сбербанка",
  "Срочно",
];

interface MFO {
  name: string;
  slug: string;
  rating: number;
  reviews: number;
  badges: { label: string; tone: "amber" | "green" }[];
  amount: string;
  term: string;
  rate: string;
  approval: string;
  letter: string;
  bg: string;
}

const mfos: MFO[] = [
  {
    name: "Займер",
    slug: "zaymer",
    rating: 4.8,
    reviews: 238,
    badges: [
      { label: "Первый займ 0%", tone: "amber" },
      { label: "Топ выбор", tone: "green" },
    ],
    amount: "до 30 000 ₽",
    term: "до 30 дней",
    rate: "от 0%",
    approval: "95%",
    letter: "З",
    bg: "from-brand-blue to-brand-green",
  },
  {
    name: "Webbankir",
    slug: "webbankir",
    rating: 4.7,
    reviews: 412,
    badges: [{ label: "Первый займ 0%", tone: "amber" }],
    amount: "до 100 000 ₽",
    term: "до 168 дней",
    rate: "от 0%",
    approval: "92%",
    letter: "W",
    bg: "from-brand-amber to-brand-green",
  },
  {
    name: "МигКредит",
    slug: "migcredit",
    rating: 4.6,
    reviews: 187,
    badges: [{ label: "На карту 24/7", tone: "green" }],
    amount: "до 50 000 ₽",
    term: "до 168 дней",
    rate: "от 0,8%",
    approval: "88%",
    letter: "М",
    bg: "from-brand-blue to-brand-blue/60",
  },
  {
    name: "Лайм-Займ",
    slug: "lime-zaim",
    rating: 4.5,
    reviews: 156,
    badges: [{ label: "Без отказа", tone: "green" }],
    amount: "до 70 000 ₽",
    term: "до 168 дней",
    rate: "от 1%",
    approval: "90%",
    letter: "Л",
    bg: "from-brand-green to-brand-green/60",
  },
  {
    name: "EzaemOnline",
    slug: "ezaem",
    rating: 4.4,
    reviews: 98,
    badges: [
      { label: "Первый займ 0%", tone: "amber" },
      { label: "Срочно", tone: "green" },
    ],
    amount: "до 30 000 ₽",
    term: "до 30 дней",
    rate: "от 0%",
    approval: "94%",
    letter: "E",
    bg: "from-brand-amber to-brand-blue",
  },
  {
    name: "VIVA Деньги",
    slug: "viva",
    rating: 4.3,
    reviews: 312,
    badges: [{ label: "С плохой КИ", tone: "green" }],
    amount: "до 30 000 ₽",
    term: "до 60 дней",
    rate: "от 0,9%",
    approval: "87%",
    letter: "V",
    bg: "from-brand-blue to-brand-amber",
  },
  {
    name: "Турбозайм",
    slug: "turbozaym",
    rating: 4.5,
    reviews: 224,
    badges: [{ label: "Срочно за 5 мин", tone: "amber" }],
    amount: "до 15 000 ₽",
    term: "до 30 дней",
    rate: "от 1%",
    approval: "93%",
    letter: "Т",
    bg: "from-brand-green to-brand-blue",
  },
  {
    name: "Кредит Плюс",
    slug: "credit-plus",
    rating: 4.2,
    reviews: 145,
    badges: [{ label: "На карту", tone: "green" }],
    amount: "до 50 000 ₽",
    term: "до 90 дней",
    rate: "от 0,8%",
    approval: "85%",
    letter: "К",
    bg: "from-brand-amber to-brand-green",
  },
];

function Stars({ rating }: { rating: number }) {
  return (
    <div className="flex items-center gap-0.5">
      {[1, 2, 3, 4, 5].map((i) => (
        <Star
          key={i}
          className={cn(
            "h-3.5 w-3.5",
            i <= Math.round(rating)
              ? "fill-brand-amber text-brand-amber"
              : "fill-brand-line text-brand-line",
          )}
        />
      ))}
    </div>
  );
}

export function MfoCatalogSection() {
  const [active, setActive] = useState("Все");

  return (
    <section className="px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Лучшие МФО 2026 года
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Топ микрофинансовых организаций по нашему рейтингу
          </p>
        </div>

        <div className="mt-8 -mx-6 px-6 md:mx-0 md:px-0">
          <div className="flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden md:flex-wrap md:justify-center md:overflow-visible">
            {filters.map((f) => (
              <button
                key={f}
                onClick={() => setActive(f)}
                className={cn(
                  "shrink-0 rounded-pill border px-4 py-2 text-sm font-bold transition-all",
                  active === f
                    ? "border-brand-green bg-brand-green text-white shadow-card"
                    : "border-brand-line bg-white text-brand-ink hover:border-brand-blue hover:text-brand-blue",
                )}
              >
                {f}
              </button>
            ))}
          </div>
        </div>

        <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {mfos.map((m) => (
            <div
              key={m.name}
              className="group flex flex-col rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-hover"
            >
              <div className="flex items-start justify-between">
                <div
                  className={cn(
                    "flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl font-extrabold text-white shadow-card",
                    m.bg,
                  )}
                >
                  {m.letter}
                </div>
                <div className="text-right">
                  <Stars rating={m.rating} />
                  <div className="mt-1 text-sm font-extrabold text-brand-ink">
                    {m.rating}
                  </div>
                  <div className="text-[11px] font-medium text-brand-muted">
                    ({m.reviews} отзывов)
                  </div>
                </div>
              </div>

              <h3 className="mt-4 text-[22px] font-extrabold leading-tight text-brand-ink">
                <Link to="/mfo/$slug" params={{ slug: m.slug }} className="hover:text-brand-blue">
                  {m.name}
                </Link>
              </h3>

              <div className="mt-3 flex flex-wrap gap-1.5">
                {m.badges.map((b) => (
                  <span
                    key={b.label}
                    className={cn(
                      "inline-flex items-center rounded-pill px-2.5 py-1 text-[11px] font-bold ring-1 ring-inset",
                      b.tone === "amber"
                        ? "bg-brand-amber/15 text-[#9a6300] ring-brand-amber/30"
                        : "bg-brand-green/12 text-brand-green ring-brand-green/20",
                    )}
                  >
                    {b.label}
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
              <Link
                to="/mfo/$slug"
                params={{ slug: m.slug }}
                className="mt-2 flex h-11 w-full items-center justify-center gap-2 rounded-pill border border-brand-line bg-white text-sm font-bold text-brand-ink transition-all hover:border-brand-blue hover:bg-brand-soft hover:text-brand-blue"
              >
                <FileText className="h-4 w-4" strokeWidth={2.5} /> Обзор {m.name}
              </Link>
              <p className="mt-2.5 text-center text-[11px] font-medium text-brand-muted">
                Заявка за 5 минут • Без справок
              </p>
            </div>
          ))}
        </div>

        <div className="mt-10 text-center">
          <button className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-6 text-sm font-bold text-brand-ink transition-all hover:border-brand-blue hover:text-brand-blue">
            Показать ещё 38 МФО <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </button>
        </div>
      </div>
    </section>
  );
}

function Row({ k, v, highlight }: { k: string; v: string; highlight?: boolean }) {
  return (
    <div className="flex items-baseline justify-between">
      <dt className="text-xs font-semibold text-brand-muted">{k}:</dt>
      <dd
        className={cn(
          "text-sm font-extrabold",
          highlight ? "text-brand-green" : "text-brand-ink",
        )}
      >
        {v}
      </dd>
    </div>
  );
}
