import { ArrowRight, Mail } from "lucide-react";

export function NewsletterCta() {
  return (
    <section className="px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div
          className="relative overflow-hidden rounded-[28px] p-8 shadow-hover sm:p-12 md:p-16"
          style={{
            background:
              "radial-gradient(circle at 85% 20%, rgba(255,255,255,0.18), transparent 40%), radial-gradient(circle at 10% 90%, rgba(37,99,235,0.35), transparent 45%), linear-gradient(135deg, #10b981 0%, #059669 50%, #0f766e 100%)",
          }}
        >
          {/* Decorative blobs */}
          <div className="pointer-events-none absolute -right-20 -top-20 h-60 w-60 rounded-full bg-white/10 blur-3xl" />
          <div className="pointer-events-none absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-brand-blue/30 blur-3xl" />

          <div className="relative grid gap-8 md:grid-cols-[1.4fr_1fr] md:items-center">
            <div>
              <span className="inline-flex items-center gap-2 rounded-pill bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white backdrop-blur">
                <Mail className="h-3.5 w-3.5" /> Рассылка
              </span>
              <h2 className="mt-4 text-3xl font-extrabold leading-tight text-white md:text-4xl">
                Узнавайте о лучших предложениях первыми
              </h2>
              <p className="mt-3 max-w-xl text-base text-white/90 md:text-lg">
                Раз в неделю — топ МФО недели и эксклюзивные предложения 0%
              </p>
            </div>

            <form
              onSubmit={(e) => e.preventDefault()}
              className="flex h-14 items-center gap-1 rounded-pill bg-white p-1.5 shadow-hover"
            >
              <input
                type="email"
                placeholder="ваш@email.ru"
                className="min-w-0 flex-1 bg-transparent px-4 text-sm font-semibold text-brand-ink placeholder:text-brand-muted focus:outline-none"
                aria-label="Email"
              />
              <button
                type="submit"
                className="inline-flex h-11 shrink-0 items-center gap-2 rounded-pill bg-brand-ink px-5 text-sm font-bold text-white transition-all hover:bg-brand-dark active:scale-[0.97]"
              >
                Подписаться <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
}
