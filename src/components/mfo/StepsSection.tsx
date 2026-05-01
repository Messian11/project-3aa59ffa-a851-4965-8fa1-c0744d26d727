import { ArrowRight } from "lucide-react";

const steps = [
  { n: 1, emoji: "📝", title: "Регистрация", desc: "Заполните анкету на сайте Займера" },
  { n: 2, emoji: "✅", title: "Подтверждение", desc: "Подтвердите номер телефона" },
  { n: 3, emoji: "💳", title: "Карта", desc: "Привяжите карту для получения денег" },
  { n: 4, emoji: "⚡", title: "Решение", desc: "Получите решение за 5 минут" },
  { n: 5, emoji: "💰", title: "Деньги", desc: "Деньги придут на карту мгновенно" },
];

export function StepsSection() {
  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Как оформить займ в Займере за 5 шагов
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Весь процесс — онлайн, без визита в офис
          </p>
        </div>

        <ol className="mt-12 grid gap-6 lg:grid-cols-5">
          {steps.map((s, i) => (
            <li
              key={s.n}
              className="relative rounded-2xl border border-brand-line bg-white p-5 shadow-card transition-all hover:-translate-y-1 hover:shadow-hover"
            >
              <span className="absolute -top-3 left-5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-brand-blue text-xs font-extrabold text-white shadow-card">
                {s.n}
              </span>
              <div className="mt-2 text-3xl">{s.emoji}</div>
              <h3 className="mt-3 text-lg font-extrabold text-brand-ink">{s.title}</h3>
              <p className="mt-1 text-sm font-medium text-brand-muted">{s.desc}</p>
              {i < steps.length - 1 && (
                <ArrowRight
                  className="absolute -right-4 top-1/2 hidden h-5 w-5 -translate-y-1/2 text-brand-line lg:block"
                  strokeWidth={2.5}
                />
              )}
            </li>
          ))}
        </ol>

        <div className="mt-10 text-center">
          <button className="inline-flex h-14 items-center justify-center gap-2 rounded-pill bg-brand-green px-7 text-base font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
            Начать оформление <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
          </button>
        </div>
      </div>
    </section>
  );
}
