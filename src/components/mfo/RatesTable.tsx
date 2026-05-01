const rows = [
  ["Ставка в день", "0%", "0,99%"],
  ["Сумма", "до 30 000 ₽", "до 30 000 ₽"],
  ["Срок", "до 30 дней", "до 30 дней"],
  ["Переплата за 30 дней", "0 ₽", "8 910 ₽"],
  ["ПСК (полная стоимость кредита)", "0% годовых", "361,35% годовых"],
];

export function RatesTable() {
  return (
    <section className="bg-brand-soft px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Тарифы Займера
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Прозрачное сравнение первого и повторных займов
          </p>
        </div>

        {/* Desktop table */}
        <div className="mt-10 hidden overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card md:block">
          <table className="w-full">
            <thead>
              <tr className="bg-brand-ink text-white">
                <th className="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wider">
                  Условие
                </th>
                <th className="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wider">
                  Первый займ
                </th>
                <th className="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wider">
                  Повторный займ
                </th>
              </tr>
            </thead>
            <tbody>
              {rows.map(([cond, first, repeat], i) => (
                <tr key={cond} className={i % 2 === 0 ? "bg-white" : "bg-brand-soft"}>
                  <td className="px-6 py-4 text-sm font-semibold text-brand-muted">
                    {cond}
                  </td>
                  <td className="px-6 py-4 text-base font-extrabold text-brand-green">
                    {first}
                  </td>
                  <td className="px-6 py-4 text-base font-extrabold text-brand-ink">
                    {repeat}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Mobile cards */}
        <div className="mt-8 grid gap-4 md:hidden">
          {[
            { title: "Первый займ", tone: "green" as const },
            { title: "Повторный займ", tone: "ink" as const },
          ].map((col, idx) => (
            <div
              key={col.title}
              className="rounded-2xl border border-brand-line bg-white p-5 shadow-card"
            >
              <div
                className={
                  col.tone === "green"
                    ? "inline-flex rounded-pill bg-brand-green/12 px-3 py-1 text-xs font-bold text-brand-green"
                    : "inline-flex rounded-pill bg-brand-soft px-3 py-1 text-xs font-bold text-brand-muted"
                }
              >
                {col.title}
              </div>
              <dl className="mt-4 divide-y divide-brand-line">
                {rows.map(([cond, first, repeat]) => (
                  <div
                    key={cond}
                    className="flex items-baseline justify-between gap-4 py-2.5 first:pt-0"
                  >
                    <dt className="text-xs font-semibold text-brand-muted">{cond}</dt>
                    <dd
                      className={
                        col.tone === "green"
                          ? "text-sm font-extrabold text-brand-green"
                          : "text-sm font-extrabold text-brand-ink"
                      }
                    >
                      {idx === 0 ? first : repeat}
                    </dd>
                  </div>
                ))}
              </dl>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
