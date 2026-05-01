import { ArrowRight, Lock, Zap, FileCheck } from "lucide-react";

export function FinalCta() {
  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-7xl">
        <div
          className="relative overflow-hidden rounded-[28px] p-8 text-center shadow-hover sm:p-14"
          style={{
            background:
              "radial-gradient(circle at 85% 20%, rgba(255,255,255,0.18), transparent 40%), radial-gradient(circle at 10% 90%, rgba(37,99,235,0.35), transparent 45%), linear-gradient(135deg, #10b981 0%, #059669 50%, #0f766e 100%)",
          }}
        >
          <div className="pointer-events-none absolute -right-20 -top-20 h-60 w-60 rounded-full bg-white/10 blur-3xl" />
          <div className="pointer-events-none absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-brand-blue/30 blur-3xl" />

          <div className="relative">
            <h2 className="mx-auto max-w-2xl text-3xl font-extrabold leading-tight text-white md:text-4xl">
              Готовы получить займ в Займере?
            </h2>
            <p className="mx-auto mt-3 max-w-xl text-base text-white/90 md:text-lg">
              Заполните заявку прямо сейчас и получите деньги за 5-15 минут
            </p>

            <button className="mt-8 inline-flex h-14 items-center justify-center gap-2 rounded-pill bg-white px-8 text-base font-extrabold text-brand-green shadow-hover transition-all hover:scale-[1.02] hover:bg-white/95 active:scale-[0.98]">
              Оформить займ <ArrowRight className="h-5 w-5" strokeWidth={2.5} />
            </button>

            <div className="mt-7 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-sm font-bold text-white">
              <span className="inline-flex items-center gap-1.5">
                <Lock className="h-4 w-4" /> Безопасно
              </span>
              <span className="inline-flex items-center gap-1.5">
                <Zap className="h-4 w-4" /> За 5 минут
              </span>
              <span className="inline-flex items-center gap-1.5">
                <FileCheck className="h-4 w-4" /> Без справок
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
