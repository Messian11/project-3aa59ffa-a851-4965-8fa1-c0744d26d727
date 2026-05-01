import { ArrowRight, BadgeCheck, Star } from "lucide-react";

const categories = [
  ["Одобрение", 5],
  ["Скорость", 5],
  ["Условия", 4],
  ["Поддержка", 5],
  ["Надёжность", 5],
] as const;

const reviews = [
  {
    name: "Анна К.",
    initials: "АК",
    bg: "from-brand-blue to-brand-green",
    rating: 5,
    date: "27 апреля 2026",
    text: "Брала первый займ 15 000 ₽ — действительно под 0%. Заявку рассмотрели за 3 минуты, деньги пришли на карту Сбербанка мгновенно. Вернула вовремя — никаких скрытых комиссий. Очень довольна!",
  },
  {
    name: "Дмитрий В.",
    initials: "ДВ",
    bg: "from-brand-amber to-brand-blue",
    rating: 4,
    date: "22 апреля 2026",
    text: "Использую Займер уже больше года. Первый займ был под 0%, потом ставки стандартные. Главный плюс — быстрое одобрение даже когда другие отказывают. Минус — лимит всего 30 000 ₽, для крупных целей не подходит.",
  },
  {
    name: "Светлана М.",
    initials: "СМ",
    bg: "from-brand-green to-brand-blue",
    rating: 5,
    date: "18 апреля 2026",
    text: "После закрытия в одном банке думала, что мне нигде не одобрят. Займер выдал 20 000 ₽ за 5 минут. Сайт удобный, всё через телефон, никуда ехать не пришлось. Рекомендую!",
  },
];

function Stars({ rating, size = 4 }: { rating: number; size?: 4 | 5 }) {
  return (
    <div className="flex items-center gap-0.5">
      {[1, 2, 3, 4, 5].map((i) => (
        <Star
          key={i}
          className={
            i <= rating
              ? `h-${size} w-${size} fill-brand-amber text-brand-amber`
              : `h-${size} w-${size} fill-brand-line text-brand-line`
          }
        />
      ))}
    </div>
  );
}

export function ReviewsSection() {
  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Отзывы клиентов Займера
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Реальные отзывы с проверкой через Госуслуги
          </p>
        </div>

        {/* Average rating card */}
        <div className="mt-10 grid gap-6 rounded-2xl border border-brand-line bg-white p-7 shadow-card md:grid-cols-[260px_1fr] md:p-9">
          <div className="flex flex-col items-center justify-center border-b border-brand-line pb-6 md:border-b-0 md:border-r md:pb-0 md:pr-6">
            <div className="text-6xl font-extrabold tracking-tight text-brand-ink">
              4.8
            </div>
            <div className="mt-2">
              <div className="flex items-center gap-0.5">
                {[1, 2, 3, 4, 5].map((i) => (
                  <Star key={i} className="h-5 w-5 fill-brand-amber text-brand-amber" />
                ))}
              </div>
            </div>
            <div className="mt-2 text-sm font-semibold text-brand-muted">
              238 отзывов
            </div>
          </div>
          <div className="grid gap-3 sm:grid-cols-2">
            {categories.map(([label, score]) => (
              <div
                key={label}
                className="flex items-center justify-between rounded-xl bg-brand-soft px-4 py-3"
              >
                <span className="text-sm font-bold text-brand-ink">{label}</span>
                <Stars rating={score} />
              </div>
            ))}
          </div>
        </div>

        <div className="mt-8 grid gap-5 md:grid-cols-3">
          {reviews.map((r) => (
            <article
              key={r.name}
              className="flex flex-col rounded-2xl border border-brand-line bg-white p-6 shadow-card transition-shadow hover:shadow-hover"
            >
              <div className="flex items-center gap-3">
                <div
                  className={`flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br ${r.bg} text-sm font-extrabold text-white`}
                >
                  {r.initials}
                </div>
                <div>
                  <div className="flex items-center gap-1.5">
                    <span className="text-sm font-extrabold text-brand-ink">
                      {r.name}
                    </span>
                    <BadgeCheck className="h-4 w-4 text-brand-blue" />
                  </div>
                  <Stars rating={r.rating} />
                </div>
              </div>
              <div className="mt-3 text-xs font-semibold text-brand-muted">
                {r.date}
              </div>
              <p className="mt-3 text-sm font-medium leading-relaxed text-brand-ink">
                {r.text}
              </p>
            </article>
          ))}
        </div>

        <div className="mt-8 text-center">
          <button className="inline-flex h-12 items-center justify-center gap-2 rounded-pill border border-brand-line bg-white px-6 text-sm font-bold text-brand-ink transition-all hover:border-brand-blue hover:text-brand-blue">
            Показать все 238 отзывов <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </button>
        </div>
      </div>
    </section>
  );
}
