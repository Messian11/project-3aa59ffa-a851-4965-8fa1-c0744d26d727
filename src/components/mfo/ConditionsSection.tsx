import { Banknote, UserCheck, Wallet, CheckCircle2 } from "lucide-react";

const cards = [
  {
    icon: Banknote,
    title: "Сумма и срок",
    rows: [
      ["Сумма займа", "от 1 000 до 30 000 ₽"],
      ["Срок", "от 7 до 30 дней"],
      ["Первый займ под 0%", "да"],
      ["Повторные займы", "от 0,99% в день"],
    ],
  },
  {
    icon: UserCheck,
    title: "Требования",
    rows: [
      ["Возраст", "от 18 до 70 лет"],
      ["Гражданство РФ", "обязательно"],
      ["Прописка", "любой регион РФ"],
      ["Документы", "паспорт + СНИЛС"],
    ],
  },
  {
    icon: Wallet,
    title: "Способы получения",
    rows: [
      ["На карту любого банка", "✓"],
      ["На QIWI кошелёк", "✓"],
      ["Наличными в Контакт", "✓"],
      ["На счёт банка", "✓"],
    ],
  },
  {
    icon: CheckCircle2,
    title: "Одобрение",
    rows: [
      ["Процент одобрений", "95%"],
      ["Время на решение", "5 минут"],
      ["Решение по плохой КИ", "да"],
      ["Без справок о доходах", "да"],
    ],
  },
];

export function ConditionsSection() {
  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Условия займа
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Все детали в одном месте — никаких скрытых условий
          </p>
        </div>

        <div className="mt-10 grid gap-5 md:grid-cols-2">
          {cards.map((c) => (
            <div
              key={c.title}
              className="rounded-2xl border border-brand-line bg-white p-6 shadow-card transition-shadow hover:shadow-hover"
            >
              <div className="flex items-center gap-3">
                <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-blue/10 text-brand-blue">
                  <c.icon className="h-5 w-5" strokeWidth={2.25} />
                </span>
                <h3 className="text-xl font-extrabold text-brand-ink">{c.title}</h3>
              </div>
              <dl className="mt-5 divide-y divide-brand-line">
                {c.rows.map(([k, v]) => (
                  <div
                    key={k}
                    className="flex items-baseline justify-between gap-4 py-3 first:pt-0"
                  >
                    <dt className="text-sm font-semibold text-brand-muted">{k}</dt>
                    <dd className="text-right text-sm font-extrabold text-brand-ink">
                      {v}
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
