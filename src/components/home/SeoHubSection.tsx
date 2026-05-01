const columns = [
  {
    title: "По сумме",
    links: [
      "Займ 1 000 ₽",
      "Займ 3 000 ₽",
      "Займ 5 000 ₽",
      "Займ 10 000 ₽",
      "Займ 15 000 ₽",
      "Займ 30 000 ₽",
      "Займ 50 000 ₽",
      "Займ 100 000 ₽",
    ],
  },
  {
    title: "По городам",
    links: [
      "МФО в Москве",
      "МФО в СПб",
      "МФО в Казани",
      "МФО в Новосибирске",
      "МФО в Екатеринбурге",
      "МФО в Краснодаре",
      "МФО в Уфе",
      "Все города",
    ],
  },
  {
    title: "По ситуации",
    links: [
      "Без отказа",
      "С плохой КИ",
      "Пенсионерам",
      "Студентам",
      "Без справок",
      "Без поручителей",
      "Срочно за 5 минут",
      "На карту 24/7",
    ],
  },
  {
    title: "ТОП МФО",
    links: [
      "Займер",
      "Webbankir",
      "МигКредит",
      "Лайм-Займ",
      "EzaemOnline",
      "VIVA Деньги",
      "Турбозайм",
      "Кредит Плюс",
    ],
  },
];

export function SeoHubSection() {
  return (
    <section
      className="px-6 py-20"
      style={{
        background:
          "radial-gradient(circle at 80% 20%, rgba(16,185,129,0.18), transparent 35%), radial-gradient(circle at 10% 90%, rgba(37,99,235,0.22), transparent 40%), linear-gradient(135deg, #0f172a 0%, #133b52 100%)",
      }}
    >
      <div className="mx-auto max-w-7xl">
        <div className="max-w-2xl">
          <h2 className="text-3xl font-extrabold tracking-tight text-white md:text-4xl">
            SEO-навигация по сайту
          </h2>
          <p className="mt-3 text-base text-white/70 md:text-lg">
            Полный путеводитель по нашему каталогу
          </p>
        </div>

        <div className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
          {columns.map((c) => (
            <div key={c.title}>
              <h3 className="text-xs font-extrabold uppercase tracking-[0.18em] text-brand-green">
                {c.title}
              </h3>
              <ul className="mt-5 space-y-3">
                {c.links.map((l) => (
                  <li key={l}>
                    <a
                      href="#"
                      className="text-sm font-semibold text-white/80 transition-colors hover:text-white"
                    >
                      {l}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
