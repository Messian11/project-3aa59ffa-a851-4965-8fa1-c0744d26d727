import { ArrowRight, Calendar, Clock } from "lucide-react";
import { cn } from "@/lib/utils";

const posts = [
  {
    cat: "Гид",
    title: "Как выбрать МФО — пошаговый гид 2026",
    excerpt:
      "Разбираем ключевые критерии выбора микрофинансовой организации: лицензия ЦБ, ставки, отзывы и скрытые комиссии.",
    date: "29 апреля 2026",
    read: "8 мин чтения",
    bg: "from-brand-blue/30 via-brand-green/20 to-brand-amber/20",
  },
  {
    cat: "Сравнение",
    title: "Топ-10 МФО с одобрением 95% — рейтинг",
    excerpt:
      "Сравнили статистику одобрения 50 микрофинансовых организаций и выбрали те, где шанс получить деньги максимальный.",
    date: "27 апреля 2026",
    read: "12 мин чтения",
    bg: "from-brand-green/30 via-brand-blue/20 to-brand-soft",
  },
  {
    cat: "Новости",
    title: "Изменения в законе об МФО с июля 2026",
    excerpt:
      "ЦБ ужесточил требования к микрофинансовым организациям. Что изменится для заёмщиков и какие ставки ждать.",
    date: "25 апреля 2026",
    read: "6 мин чтения",
    bg: "from-brand-amber/30 via-brand-blue/20 to-brand-green/20",
  },
];

const catTones: Record<string, string> = {
  Гид: "bg-brand-blue text-white",
  Сравнение: "bg-brand-green text-white",
  Новости: "bg-brand-amber text-[#1f1300]",
};

export function BlogSection() {
  return (
    <section className="bg-brand-soft px-6 py-20">
      <div className="mx-auto max-w-7xl">
        <div className="flex items-end justify-between gap-4">
          <div>
            <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
              Полезные статьи
            </h2>
            <p className="mt-3 text-base text-brand-muted md:text-lg">
              Гайды, новости и сравнения от наших экспертов
            </p>
          </div>
          <a
            href="#"
            className="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-brand-blue hover:text-brand-green sm:inline-flex"
          >
            Все статьи <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </a>
        </div>

        <div className="mt-10 grid gap-6 md:grid-cols-3">
          {posts.map((p) => (
            <a
              key={p.title}
              href="#"
              className="group flex flex-col overflow-hidden rounded-2xl border border-brand-line bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-hover"
            >
              <div className={cn("relative aspect-[16/9] bg-gradient-to-br", p.bg)}>
                <span
                  className={cn(
                    "absolute left-4 top-4 inline-flex items-center rounded-pill px-3 py-1 text-[11px] font-bold uppercase tracking-wider",
                    catTones[p.cat],
                  )}
                >
                  {p.cat}
                </span>
              </div>
              <div className="flex flex-1 flex-col p-6">
                <h3 className="text-xl font-extrabold leading-tight text-brand-ink group-hover:text-brand-blue">
                  {p.title}
                </h3>
                <p className="mt-2 line-clamp-2 text-sm font-medium text-brand-muted">
                  {p.excerpt}
                </p>
                <div className="mt-auto flex items-center gap-4 pt-5 text-xs font-semibold text-brand-muted">
                  <span className="inline-flex items-center gap-1.5">
                    <Calendar className="h-3.5 w-3.5" /> {p.date}
                  </span>
                  <span className="inline-flex items-center gap-1.5">
                    <Clock className="h-3.5 w-3.5" /> {p.read}
                  </span>
                </div>
              </div>
            </a>
          ))}
        </div>

        <div className="mt-8 text-center sm:hidden">
          <a href="#" className="inline-flex items-center gap-1.5 text-sm font-bold text-brand-blue">
            Все статьи <ArrowRight className="h-4 w-4" strokeWidth={2.5} />
          </a>
        </div>
      </div>
    </section>
  );
}
