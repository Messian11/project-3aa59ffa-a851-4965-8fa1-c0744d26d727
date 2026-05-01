const amounts = [
  "1 000 ₽",
  "3 000 ₽",
  "5 000 ₽",
  "10 000 ₽",
  "15 000 ₽",
  "20 000 ₽",
  "30 000 ₽",
  "50 000 ₽",
  "70 000 ₽",
  "100 000 ₽",
];

export function AmountQuickLinks() {
  return (
    <section className="px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Выберите нужную сумму
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Подборка МФО под конкретный размер займа
          </p>
        </div>

        <div className="mx-auto mt-10 flex max-w-3xl flex-wrap justify-center gap-3">
          {amounts.map((a) => (
            <a
              key={a}
              href="#"
              className="rounded-pill border border-brand-line bg-white px-5 py-2.5 text-sm font-extrabold text-brand-ink transition-all hover:-translate-y-0.5 hover:border-brand-green hover:text-brand-green hover:shadow-card"
            >
              {a}
            </a>
          ))}
        </div>
      </div>
    </section>
  );
}
