import { Check, AlertTriangle } from "lucide-react";
import { cn } from "@/lib/utils";

const pros = [
  "Первый займ бесплатно (0%)",
  "Очень быстрое одобрение",
  "Принимают с плохой КИ",
  "Нет требований к доходу",
  "Удобный мобильный сайт",
  "Большой процент одобрений",
];

const cons = [
  "Высокая ставка после первого займа",
  "Максимум 30 000 ₽",
  "Срок только до 30 дней",
  "Нет долгосрочных займов",
];

export function ProsConsSection() {
  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Плюсы и минусы Займера
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Объективная оценка без рекламы
          </p>
        </div>

        <div className="mt-10 grid gap-5 md:grid-cols-2">
          <Card tone="green" title="Плюсы Займера" items={pros} icon={Check} />
          <Card tone="amber" title="Минусы Займера" items={cons} icon={AlertTriangle} />
        </div>
      </div>
    </section>
  );
}

function Card({
  tone,
  title,
  items,
  icon: Icon,
}: {
  tone: "green" | "amber";
  title: string;
  items: string[];
  icon: typeof Check;
}) {
  const isGreen = tone === "green";
  return (
    <div
      className={cn(
        "rounded-2xl border p-7 shadow-card",
        isGreen
          ? "border-brand-green/25 bg-gradient-to-br from-brand-green/10 to-white"
          : "border-brand-amber/30 bg-gradient-to-br from-brand-amber/12 to-white",
      )}
    >
      <div className="flex items-center gap-3">
        <span
          className={cn(
            "flex h-11 w-11 items-center justify-center rounded-2xl text-white shadow-card",
            isGreen ? "bg-brand-green" : "bg-brand-amber",
          )}
        >
          <Icon className="h-5 w-5" strokeWidth={2.5} />
        </span>
        <h3 className="text-xl font-extrabold text-brand-ink">
          {isGreen ? "✅ " : "⚠️ "}
          {title}
        </h3>
      </div>
      <ul className="mt-5 space-y-3">
        {items.map((it) => (
          <li key={it} className="flex items-start gap-3">
            <span
              className={cn(
                "mt-1.5 h-2 w-2 shrink-0 rounded-full",
                isGreen ? "bg-brand-green" : "bg-brand-amber",
              )}
            />
            <span className="text-base font-semibold text-brand-ink">{it}</span>
          </li>
        ))}
      </ul>
    </div>
  );
}
