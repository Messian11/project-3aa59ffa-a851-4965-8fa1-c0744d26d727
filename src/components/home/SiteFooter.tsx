import { Youtube, Send, Share2 } from "lucide-react";

const cols = [
  {
    title: "Каталог",
    links: ["Все МФО", "ТОП-10 МФО", "Калькулятор", "Сравнение МФО"],
  },
  {
    title: "Полезное",
    links: ["Блог", "Гайды по займам", "Глоссарий", "Калькуляторы"],
  },
  {
    title: "Компания",
    links: ["О проекте", "Команда", "Контакты", "Партнёрство"],
  },
  {
    title: "Документы",
    links: [
      "Политика конфиденциальности",
      "Пользовательское соглашение",
      "Дисклеймер",
      "Реклама на сайте",
    ],
  },
];

export function SiteFooter() {
  return (
    <footer className="bg-brand-dark px-6 pt-16 pb-10 text-white/80">
      <div className="mx-auto max-w-7xl">
        <div className="grid gap-10 md:grid-cols-2 lg:grid-cols-5">
          <div className="lg:col-span-1">
            <a href="/" className="flex items-center gap-1 select-none">
              <span className="text-2xl font-extrabold text-brand-blue">Zaymi</span>
              <span className="text-2xl font-extrabold text-brand-green">Online</span>
            </a>
            <p className="mt-4 text-sm font-medium leading-relaxed text-white/65">
              Zaymi Online — независимый агрегатор МФО России. Сравниваем 50+
              компаний и помогаем выбрать лучшие условия.
            </p>
            <div className="mt-5 flex items-center gap-2">
              <SocialIcon label="VK">
                <Share2 className="h-4 w-4" />
              </SocialIcon>
              <SocialIcon label="Telegram">
                <Send className="h-4 w-4" />
              </SocialIcon>
              <SocialIcon label="YouTube">
                <Youtube className="h-4 w-4" />
              </SocialIcon>
            </div>
          </div>

          {cols.map((c) => (
            <div key={c.title}>
              <h3 className="text-xs font-extrabold uppercase tracking-[0.16em] text-white">
                {c.title}
              </h3>
              <ul className="mt-5 space-y-3">
                {c.links.map((l) => (
                  <li key={l}>
                    <a
                      href="#"
                      className="text-sm font-semibold text-white/65 transition-colors hover:text-white"
                    >
                      {l}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-14 border-t border-white/10 pt-8 text-center">
          <div className="text-sm font-semibold text-white/70">
            © 2026 Zaymi Online. Все права защищены.
          </div>
          <p className="mx-auto mt-4 max-w-4xl text-xs leading-relaxed text-white/50">
            Сайт является информационным агрегатором. Мы НЕ выдаём займы. Все
            МФО — наши партнёры. Решения о выдаче принимают исключительно МФО
            на основании внутренних правил. Возрастное ограничение: 18+.
          </p>
        </div>
      </div>
    </footer>
  );
}

function SocialIcon({ children, label }: { children: React.ReactNode; label: string }) {
  return (
    <a
      href="#"
      aria-label={label}
      className="flex h-9 w-9 items-center justify-center rounded-full bg-white/8 text-white/80 transition-all hover:bg-brand-green hover:text-white"
    >
      {children}
    </a>
  );
}
