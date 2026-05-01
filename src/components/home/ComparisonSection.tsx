import { ArrowRight } from "lucide-react";

const rows = [
  { name: "Займер", letter: "З", bg: "from-brand-blue to-brand-green", amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "95%" },
  { name: "Webbankir", letter: "W", bg: "from-brand-amber to-brand-green", amount: "до 100 000 ₽", term: "до 168 дней", rate: "от 0%", approval: "92%" },
  { name: "МигКредит", letter: "М", bg: "from-brand-blue to-brand-blue/60", amount: "до 50 000 ₽", term: "до 168 дней", rate: "от 0,8%", approval: "88%" },
  { name: "Лайм-Займ", letter: "Л", bg: "from-brand-green to-brand-green/60", amount: "до 70 000 ₽", term: "до 168 дней", rate: "от 1%", approval: "90%" },
  { name: "EzaemOnline", letter: "E", bg: "from-brand-amber to-brand-blue", amount: "до 30 000 ₽", term: "до 30 дней", rate: "от 0%", approval: "94%" },
  { name: "VIVA Деньги", letter: "V", bg: "from-brand-blue to-brand-amber", amount: "до 30 000 ₽", term: "до 60 дней", rate: "от 0,9%", approval: "87%" },
  { name: "Турбозайм", letter: "Т", bg: "from-brand-green to-brand-blue", amount: "до 15 000 ₽", term: "до 30 дней", rate: "от 1%", approval: "93%" },
  { name: "Кредит Плюс", letter: "К", bg: "from-brand-amber to-brand-green", amount: "до 50 000 ₽", term: "до 90 дней", rate: "от 0,8%", approval: "85%" },
];

export function ComparisonSection() {
  return (
    <section className="bg-brand-soft px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Сравнение МФО
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Все ключевые условия в одной таблице
          </p>
        </div>

        {/* Desktop table */}
        <div className="mt-10 hidden overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card lg:block">
          <table className="w-full">
            <thead>
              <tr className="bg-brand-ink text-white">
                <th className="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">МФО</th>
                <th className="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Сумма</th>
                <th className="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Срок</th>
                <th className="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Ставка</th>
                <th className="px-5 py-4 text-left text-xs font-extrabold uppercase tracking-wider">Одобрение</th>
                <th className="px-5 py-4" />
              </tr>
            </thead>
            <tbody>
              {rows.map((r, i) => (
                <tr
                  key={r.name}
                  className={i % 2 === 0 ? "bg-white" : "bg-brand-soft"}
                >
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-3">
                      <div className={`flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br ${r.bg} text-base font-extrabold text-white`}>
                        {r.letter}
                      </div>
                      <span className="text-base font-extrabold text-brand-ink">{r.name}</span>
                    </div>
                  </td>
                  <td className="px-5 py-4 text-sm font-bold text-brand-ink">{r.amount}</td>
                  <td className="px-5 py-4 text-sm font-bold text-brand-ink">{r.term}</td>
                  <td className="px-5 py-4 text-sm font-extrabold text-brand-green">{r.rate}</td>
                  <td className="px-5 py-4 text-sm font-bold text-brand-ink">{r.approval}</td>
                  <td className="px-5 py-4 text-right">
                    <button className="inline-flex h-10 items-center gap-1.5 rounded-pill bg-brand-green px-4 text-xs font-bold text-white transition-all hover:bg-brand-green/90 active:scale-[0.97]">
                      Получить <ArrowRight className="h-3.5 w-3.5" strokeWidth={2.5} />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Mobile cards */}
        <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:hidden">
          {rows.map((r) => (
            <div key={r.name} className="rounded-2xl border border-brand-line bg-white p-5 shadow-card">
              <div className="flex items-center gap-3">
                <div className={`flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br ${r.bg} text-lg font-extrabold text-white`}>
                  {r.letter}
                </div>
                <div className="text-lg font-extrabold text-brand-ink">{r.name}</div>
              </div>
              <dl className="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-brand-soft p-3 text-sm">
                <div><dt className="text-[11px] font-semibold text-brand-muted">Сумма</dt><dd className="font-extrabold text-brand-ink">{r.amount}</dd></div>
                <div><dt className="text-[11px] font-semibold text-brand-muted">Срок</dt><dd className="font-extrabold text-brand-ink">{r.term}</dd></div>
                <div><dt className="text-[11px] font-semibold text-brand-muted">Ставка</dt><dd className="font-extrabold text-brand-green">{r.rate}</dd></div>
                <div><dt className="text-[11px] font-semibold text-brand-muted">Одобрение</dt><dd className="font-extrabold text-brand-ink">{r.approval}</dd></div>
              </dl>
              <button className="mt-4 flex h-11 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-sm font-bold text-white">
                Получить займ <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
              </button>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
