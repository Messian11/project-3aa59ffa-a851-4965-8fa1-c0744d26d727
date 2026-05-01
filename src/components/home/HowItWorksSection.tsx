const steps = [
  {
    n: 1,
    emoji: "📝",
    title: "Выберите МФО",
    desc: "Сравните условия и выберите подходящее предложение",
  },
  {
    n: 2,
    emoji: "✏️",
    title: "Заполните заявку",
    desc: "5 минут на телефоне или компьютере",
  },
  {
    n: 3,
    emoji: "💳",
    title: "Получите деньги",
    desc: "Деньги придут на вашу карту за 5-15 минут",
  },
];

export function HowItWorksSection() {
  return (
    <section className="px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Как это работает за 3 шага
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            От заявки до денег на карте — за считанные минуты
          </p>
        </div>

        <div className="relative mt-14 grid gap-10 md:grid-cols-3">
          {/* Connecting dotted line on desktop */}
          <div className="absolute top-9 left-[16%] right-[16%] hidden h-px border-t-2 border-dashed border-brand-line md:block" />

          {steps.map((s) => (
            <div key={s.n} className="relative flex flex-col items-center text-center">
              <div className="relative">
                <div className="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-brand-green to-brand-green/70 text-3xl text-white shadow-hover">
                  {s.emoji}
                </div>
                <span className="absolute -right-2 -top-2 flex h-9 w-9 items-center justify-center rounded-full border-4 border-white bg-brand-blue text-sm font-extrabold text-white shadow-card">
                  {s.n}
                </span>
              </div>
              <h3 className="mt-6 text-2xl font-extrabold text-brand-ink">
                {s.title}
              </h3>
              <p className="mt-2 max-w-xs text-base font-medium text-brand-muted">
                {s.desc}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
