import { useState } from "react";
import {
  ArrowRight,
  Check,
  Shield,
  FileX,
  TrendingDown,
  CreditCard,
} from "lucide-react";
import { Slider } from "@/components/ui/slider";

const trustBadges = [
  { icon: Shield, label: "Без поручителей" },
  { icon: FileX, label: "Без справок" },
  { icon: TrendingDown, label: "С плохой КИ" },
  { icon: CreditCard, label: "На карту 24/7" },
];

export function HeroSection() {
  const [amount, setAmount] = useState([15000]);
  const [term, setTerm] = useState([30]);

  return (
    <section className="relative overflow-hidden bg-hero-gradient">
      <div className="mx-auto grid max-w-7xl gap-10 px-6 py-16 lg:grid-cols-2 lg:gap-12 lg:py-20">
        {/* Left */}
        <div className="flex flex-col justify-center">
          <span className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-green">
            ТОП-50 МФО 2026
          </span>
          <h1
            className="mt-4 font-extrabold tracking-tight text-brand-ink"
            style={{ fontSize: "clamp(34px, 5vw, 56px)", lineHeight: 1.04 }}
          >
            Займ онлайн без отказа{" "}
            <span className="bg-gradient-to-r from-brand-blue to-brand-green bg-clip-text text-transparent">
              за 5 минут
            </span>
          </h1>
          <p className="mt-5 max-w-xl text-lg font-medium text-brand-muted">
            Сравнили 50+ МФО — выбрали лучшие предложения. Получите деньги на
            карту с любой кредитной историей.
          </p>

          <div className="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
            {trustBadges.map((b) => (
              <div
                key={b.label}
                className="flex items-center gap-2 rounded-pill border border-brand-line bg-white/70 px-3 py-2 backdrop-blur-sm"
              >
                <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-green/12 text-brand-green">
                  <Check className="h-3.5 w-3.5" strokeWidth={3} />
                </span>
                <span className="text-xs font-bold text-brand-ink">
                  {b.label}
                </span>
              </div>
            ))}
          </div>

          <div className="mt-8 flex flex-wrap gap-3">
            <button className="inline-flex h-14 items-center justify-center gap-2 rounded-pill bg-brand-green px-7 text-base font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
              Подобрать займ <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
            </button>
            <button className="inline-flex h-14 items-center justify-center rounded-pill border border-brand-line bg-white px-7 text-base font-bold text-brand-ink transition-all hover:border-brand-blue hover:text-brand-blue">
              Смотреть каталог
            </button>
          </div>
        </div>

        {/* Right: calculator */}
        <div className="relative">
          <div className="absolute -inset-4 -z-10 rounded-[36px] bg-gradient-to-br from-brand-blue/20 to-brand-green/20 blur-2xl" />
          <div className="rounded-[28px] border border-white/60 bg-white/80 p-6 shadow-hover backdrop-blur-xl sm:p-8">
            <div className="mb-1 text-xs font-extrabold uppercase tracking-wider text-brand-blue">
              Калькулятор займа
            </div>
            <h3 className="text-2xl font-extrabold text-brand-ink">
              Подберите идеальные условия
            </h3>

            <div className="mt-6 space-y-7">
              <div>
                <div className="flex items-baseline justify-between">
                  <span className="text-sm font-bold text-brand-ink">
                    Сумма займа
                  </span>
                  <span className="text-3xl font-extrabold text-brand-green">
                    {amount[0].toLocaleString("ru-RU")} ₽
                  </span>
                </div>
                <Slider
                  value={amount}
                  onValueChange={setAmount}
                  min={1000}
                  max={100000}
                  step={1000}
                  className="mt-3 [&_[role=slider]]:h-5 [&_[role=slider]]:w-5 [&_[role=slider]]:border-2 [&_[role=slider]]:border-brand-green [&_[role=slider]]:bg-white"
                />
                <div className="mt-2 flex justify-between text-xs font-semibold text-brand-muted">
                  <span>1 000 ₽</span>
                  <span>100 000 ₽</span>
                </div>
              </div>

              <div>
                <div className="flex items-baseline justify-between">
                  <span className="text-sm font-bold text-brand-ink">
                    Срок займа
                  </span>
                  <span className="text-3xl font-extrabold text-brand-blue">
                    {term[0]} {pluralDays(term[0])}
                  </span>
                </div>
                <Slider
                  value={term}
                  onValueChange={setTerm}
                  min={1}
                  max={365}
                  step={1}
                  className="mt-3 [&_[role=slider]]:h-5 [&_[role=slider]]:w-5 [&_[role=slider]]:border-2 [&_[role=slider]]:border-brand-blue [&_[role=slider]]:bg-white"
                />
                <div className="mt-2 flex justify-between text-xs font-semibold text-brand-muted">
                  <span>1 день</span>
                  <span>365 дней</span>
                </div>
              </div>
            </div>

            <div className="mt-6 inline-flex items-center gap-2 rounded-pill bg-brand-green/12 px-4 py-2 text-sm font-bold text-brand-green">
              <Check className="h-4 w-4" strokeWidth={3} />
              Найдено 12 предложений
            </div>

            <button className="mt-5 flex h-14 w-full items-center justify-center gap-2 rounded-pill bg-brand-green text-base font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
              Подобрать займ <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
            </button>
            <p className="mt-3 text-center text-xs font-medium text-brand-muted">
              Бесплатно. Без отказа. Решение за 5 минут.
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

function pluralDays(n: number) {
  const mod10 = n % 10;
  const mod100 = n % 100;
  if (mod10 === 1 && mod100 !== 11) return "день";
  if ([2, 3, 4].includes(mod10) && ![12, 13, 14].includes(mod100)) return "дня";
  return "дней";
}
