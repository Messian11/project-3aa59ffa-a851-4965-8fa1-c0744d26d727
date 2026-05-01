import { ArrowRight } from "lucide-react";

const cards = [
  {
    emoji: "🚫",
    title: "Без отказа",
    desc: "МФО которые одобряют максимум заявок",
    count: 24,
  },
  {
    emoji: "📉",
    title: "С плохой кредитной историей",
    desc: "Займы с открытыми просрочками и закрытыми МФО",
    count: 18,
  },
  {
    emoji: "👴",
    title: "Пенсионерам",
    desc: "Специальные условия для пенсионеров",
    count: 9,
  },
  {
    emoji: "🎓",
    title: "Студентам и молодым",
    desc: "Займы от 18 лет без работы",
    count: 14,
  },
  {
    emoji: "📄",
    title: "Без справок и поручителей",
    desc: "Только паспорт и СНИЛС",
    count: 41,
  },
  {
    emoji: "⚡",
    title: "Срочно за 5 минут",
    desc: "Деньги на карту в течение 5-15 минут",
    count: 29,
  },
];

export function SituationsSection() {
  return (
    <section className="bg-brand-soft px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Подберём займ под вашу ситуацию
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Каждой ситуации — свои подходящие МФО
          </p>
        </div>

        <div className="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {cards.map((c) => (
            <a
              href="#"
              key={c.title}
              className="group flex flex-col rounded-2xl border border-brand-line bg-white p-7 shadow-card transition-all duration-300 hover:-translate-y-1 hover:border-brand-green/30 hover:shadow-hover"
            >
              <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-soft to-white text-4xl shadow-card">
                {c.emoji}
              </div>
              <h3 className="mt-5 text-[22px] font-extrabold leading-tight text-brand-ink">
                {c.title}
              </h3>
              <p className="mt-2 text-sm font-medium text-brand-muted">
                {c.desc}
              </p>
              <div className="mt-auto flex items-center gap-1.5 pt-5 text-sm font-bold text-brand-green">
                <span>→ {c.count} МФО</span>
                <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" strokeWidth={2.5} />
              </div>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
}
