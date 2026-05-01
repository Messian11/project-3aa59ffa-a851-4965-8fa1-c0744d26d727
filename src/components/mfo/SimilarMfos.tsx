import { ArrowRight, Star } from "lucide-react";
import { cn } from "@/lib/utils";

const similar = [
  {
    name: "Webbankir",
    letter: "W",
    bg: "from-brand-amber to-brand-green",
    rating: 4.7,
    reviews: 412,
    badge: "Первый займ 0%",
    amount: "до 100 000 ₽",
    rate: "от 0%",
  },
  {
    name: "МигКредит",
    letter: "М",
    bg: "from-brand-blue to-brand-blue/60",
    rating: 4.6,
    reviews: 187,
    badge: "На карту 24/7",
    amount: "до 50 000 ₽",
    rate: "от 0,8%",
  },
  {
    name: "Лайм-Займ",
    letter: "Л",
    bg: "from-brand-green to-brand-green/60",
    rating: 4.5,
    reviews: 156,
    badge: "Без отказа",
    amount: "до 70 000 ₽",
    rate: "от 1%",
  },
];

export function SimilarMfos() {
  return (
    <section className="bg-brand-soft px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Похожие МФО
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Если Займер не подошёл — посмотрите эти варианты
          </p>
        </div>

        <div className="mt-10 grid gap-5 md:grid-cols-3">
          {similar.map((m) => (
            <div
              key={m.name}
              className="flex flex-col rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover"
            >
              <div className="flex items-start justify-between">
                <div
                  className={cn(
                    "flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br text-xl font-extrabold text-white shadow-card",
                    m.bg,
                  )}
                >
                  {m.letter}
                </div>
                <div className="text-right">
                  <div className="flex items-center gap-1">
                    <Star className="h-3.5 w-3.5 fill-brand-amber text-brand-amber" />
                    <span className="text-sm font-extrabold text-brand-ink">
                      {m.rating}
                    </span>
                  </div>
                  <div className="text-[11px] font-medium text-brand-muted">
                    {m.reviews} отзывов
                  </div>
                </div>
              </div>
              <h3 className="mt-3 text-lg font-extrabold text-brand-ink">{m.name}</h3>
              <span className="mt-2 inline-flex w-fit items-center rounded-pill bg-brand-amber/15 px-2.5 py-1 text-[11px] font-bold text-[#9a6300] ring-1 ring-inset ring-brand-amber/30">
                {m.badge}
              </span>
              <div className="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-brand-soft p-3 text-sm">
                <div>
                  <div className="text-[11px] font-semibold text-brand-muted">Сумма</div>
                  <div className="font-extrabold text-brand-ink">{m.amount}</div>
                </div>
                <div>
                  <div className="text-[11px] font-semibold text-brand-muted">Ставка</div>
                  <div className="font-extrabold text-brand-green">{m.rate}</div>
                </div>
              </div>
              <button className="mt-4 flex h-11 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-sm font-bold text-white transition-all hover:bg-brand-green/90 active:scale-[0.97]">
                Подробнее <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
              </button>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
