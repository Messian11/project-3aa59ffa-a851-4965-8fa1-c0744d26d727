import { Check, X } from "lucide-react";
import { cn } from "@/lib/utils";

const fits = [
  "Срочно нужны деньги",
  "Плохая кредитная история",
  "Нет справок о доходах",
  "Нужна сумма до 30 000 ₽",
  "Возраст 18-70 лет",
];

const doesnt = [
  "Нужна сумма больше 30 000 ₽",
  "Нужен срок больше 30 дней",
  "Гражданство не РФ",
  "Возраст младше 18 или старше 70",
  "Открытое банкротство",
];

export function FitSection() {
  return (
    <section className="bg-brand-soft px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Кому подойдёт Займер
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Честно говорим: подходим не всем — проверьте свой случай
          </p>
        </div>

        <div className="mt-10 grid gap-5 md:grid-cols-2">
          <FitCard
            tone="green"
            icon={<Check className="h-5 w-5" strokeWidth={3} />}
            title="Подойдёт если:"
            items={fits}
          />
          <FitCard
            tone="red"
            icon={<X className="h-5 w-5" strokeWidth={3} />}
            title="Не подойдёт если:"
            items={doesnt}
          />
        </div>
      </div>
    </section>
  );
}

function FitCard({
  tone,
  icon,
  title,
  items,
}: {
  tone: "green" | "red";
  icon: React.ReactNode;
  title: string;
  items: string[];
}) {
  const isGreen = tone === "green";
  return (
    <div
      className={cn(
        "rounded-2xl border p-7 shadow-card",
        isGreen
          ? "border-brand-green/25 bg-brand-green/8"
          : "border-brand-danger/25 bg-brand-danger/6",
      )}
    >
      <div className="flex items-center gap-3">
        <span
          className={cn(
            "flex h-10 w-10 items-center justify-center rounded-full text-white",
            isGreen ? "bg-brand-green" : "bg-brand-danger",
          )}
        >
          {icon}
        </span>
        <h3 className="text-xl font-extrabold text-brand-ink">{title}</h3>
      </div>
      <ul className="mt-5 space-y-3">
        {items.map((it) => (
          <li key={it} className="flex items-start gap-3">
            <span
              className={cn(
                "mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-white",
                isGreen ? "bg-brand-green" : "bg-brand-danger",
              )}
            >
              {isGreen ? (
                <Check className="h-3 w-3" strokeWidth={3.5} />
              ) : (
                <X className="h-3 w-3" strokeWidth={3.5} />
              )}
            </span>
            <span className="text-base font-semibold text-brand-ink">{it}</span>
          </li>
        ))}
      </ul>
    </div>
  );
}
