import { ArrowRight, Star, Shield, Smartphone, CreditCard, Zap } from "lucide-react";

const stats = [
  { icon: "💰", label: "Сумма", value: "1 000 — 30 000 ₽" },
  { icon: "📅", label: "Срок", value: "7 — 30 дней" },
  { icon: "📊", label: "Ставка", value: "от 0%", highlight: true },
  { icon: "✅", label: "Одобрение", value: "95%" },
];

export function MfoHeroCard() {
  return (
    <section className="px-6 pt-6">
      <div className="mx-auto max-w-7xl">
        <div
          className="relative overflow-hidden rounded-[28px] border border-brand-line p-6 shadow-card sm:p-10"
          style={{
            background:
              "radial-gradient(circle at 85% 10%, rgba(16,185,129,0.16), transparent 35%), radial-gradient(circle at 5% 90%, rgba(37,99,235,0.13), transparent 40%), linear-gradient(180deg, #f8fbff 0%, #ffffff 100%)",
          }}
        >
          <div className="grid gap-8 lg:grid-cols-[260px_1fr] lg:gap-12">
            {/* Left: logo + rating */}
            <div className="flex flex-col items-start gap-4">
              <div className="flex h-[120px] w-[120px] items-center justify-center rounded-3xl bg-gradient-to-br from-brand-blue to-brand-green text-5xl font-extrabold text-white shadow-hover">
                З
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <div className="flex items-center gap-0.5">
                    {[1, 2, 3, 4, 5].map((i) => (
                      <Star
                        key={i}
                        className={
                          i <= 5
                            ? "h-4 w-4 fill-brand-amber text-brand-amber"
                            : "h-4 w-4 fill-brand-line text-brand-line"
                        }
                      />
                    ))}
                  </div>
                  <span className="text-lg font-extrabold text-brand-ink">4.8</span>
                  <span className="text-sm font-medium text-brand-muted">238 отзывов</span>
                </div>
                <p className="mt-3 inline-flex items-center gap-1.5 rounded-pill bg-brand-soft px-3 py-1.5 text-[11px] font-semibold text-brand-muted">
                  <Shield className="h-3 w-3" /> Лицензия ЦБ РФ № 651303045...
                </p>
              </div>
            </div>

            {/* Right: main */}
            <div>
              <span className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-blue">
                Микрофинансовая организация
              </span>
              <h1
                className="mt-3 font-extrabold tracking-tight text-brand-ink"
                style={{ fontSize: "clamp(28px, 4vw, 44px)", lineHeight: 1.05 }}
              >
                Займер — займы онлайн до{" "}
                <span className="text-brand-green">30 000 ₽</span>
              </h1>

              <div className="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                {stats.map((s) => (
                  <div
                    key={s.label}
                    className="rounded-2xl border border-brand-line bg-white p-4 shadow-card"
                  >
                    <div className="text-2xl">{s.icon}</div>
                    <div className="mt-2 text-[11px] font-semibold uppercase tracking-wider text-brand-muted">
                      {s.label}
                    </div>
                    <div
                      className={
                        s.highlight
                          ? "mt-1 text-base font-extrabold text-brand-green"
                          : "mt-1 text-base font-extrabold text-brand-ink"
                      }
                    >
                      {s.value}
                    </div>
                  </div>
                ))}
              </div>

              <button className="mt-7 inline-flex h-14 items-center justify-center gap-2 rounded-pill bg-brand-green px-7 text-base font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.98]">
                Получить займ в Займере
                <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
              </button>

              <div className="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-semibold text-brand-muted">
                <span className="inline-flex items-center gap-1.5">
                  <Zap className="h-4 w-4 text-brand-amber" /> Решение за 5 минут
                </span>
                <span className="inline-flex items-center gap-1.5">
                  <CreditCard className="h-4 w-4 text-brand-blue" /> На любую карту
                </span>
                <span className="inline-flex items-center gap-1.5">
                  <Smartphone className="h-4 w-4 text-brand-green" /> Без визита в офис
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
