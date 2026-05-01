const stats = [
  { value: "50+", label: "МФО в каталоге" },
  { value: "127 000 ₽", label: "выдано займов сегодня" },
  { value: "4.8/5", label: "средний рейтинг" },
  { value: "97%", label: "одобрений за 24ч" },
];

export function SocialProofStrip() {
  return (
    <section className="relative -mt-10 px-6 lg:-mt-14">
      <div className="mx-auto max-w-7xl">
        <div className="grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-brand-line bg-brand-line shadow-card md:grid-cols-4">
          {stats.map((s) => (
            <div
              key={s.label}
              className="bg-white p-6 text-center transition-colors hover:bg-brand-soft"
            >
              <div className="bg-gradient-to-r from-brand-blue to-brand-green bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-4xl">
                {s.value}
              </div>
              <div className="mt-1 text-xs font-semibold text-brand-muted md:text-sm">
                {s.label}
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
