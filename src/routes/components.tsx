import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import {
  X,
  Mail,
  ArrowRight,
  ArrowUp,
  Search,
  AlertTriangle,
  CheckCircle2,
  Info,
  XCircle,
  Loader2,
  Inbox,
  FileQuestion,
  ServerCrash,
  Cookie,
  Sparkles,
} from "lucide-react";
import { toast } from "sonner";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { cn } from "@/lib/utils";

export const Route = createFileRoute("/components")({
  head: () => ({
    meta: [
      { title: "Компонентная библиотека — Zaymi Online" },
      { name: "description", content: "Внутренняя библиотека UI-компонентов: формы, поп-апы, скелетоны, тосты, ошибки." },
    ],
  }),
  component: ComponentsPage,
});

/* ─────────────────────────────────────────────────── */

function ComponentsPage() {
  const [showNewsletter, setShowNewsletter] = useState(false);
  const [showCookies, setShowCookies] = useState(true);
  const [showBackTop, setShowBackTop] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const onScroll = () => setShowBackTop(window.scrollY > 400);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <div className="min-h-screen bg-background">
      <SiteHeader />

      {/* Hero */}
      <section className="border-b border-border/40 bg-gradient-to-br from-brand-blue/10 via-transparent to-brand-green/10">
        <div className="mx-auto max-w-7xl px-4 py-14">
          <span className="inline-flex items-center gap-2 rounded-full bg-brand-amber/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-amber">
            <Sparkles className="h-3.5 w-3.5" /> Component library
          </span>
          <h1 className="mt-4 text-4xl md:text-5xl font-extrabold tracking-tight text-foreground">
            Библиотека компонентов
          </h1>
          <p className="mt-3 text-lg text-muted-foreground max-w-2xl">
            8 базовых UI-блоков, используемых на всех страницах Zaymi Online.
          </p>
        </div>
      </section>

      <main className="mx-auto max-w-7xl px-4 py-16 space-y-24">
        {/* 1. CONTACT FORM */}
        <Section number="01" title="Contact Form" subtitle="Форма обратной связи">
          <ContactForm />
        </Section>

        {/* 2. NEWSLETTER POPUP */}
        <Section number="02" title="Newsletter Popup" subtitle="Поп-ап подписки на рассылку">
          <div className="rounded-3xl border border-dashed border-border bg-card p-8 text-center">
            <p className="text-muted-foreground mb-4">Кликните, чтобы открыть поп-ап:</p>
            <button
              onClick={() => setShowNewsletter(true)}
              className="inline-flex items-center gap-2 rounded-2xl bg-foreground px-6 py-3 text-sm font-bold text-background hover:opacity-90 transition-opacity"
            >
              Открыть Newsletter Popup <ArrowRight className="h-4 w-4" />
            </button>
          </div>
          {showNewsletter && <NewsletterPopup onClose={() => setShowNewsletter(false)} />}
        </Section>

        {/* 3. COOKIE BANNER */}
        <Section number="03" title="Cookie Banner" subtitle="Уведомление о cookies">
          <div className="rounded-3xl border border-dashed border-border bg-card p-6">
            <p className="text-sm text-muted-foreground mb-4">Превью (нижний бар):</p>
            <div className="relative h-40 rounded-2xl bg-gradient-to-br from-brand-blue/5 to-brand-green/5 overflow-hidden">
              <div className="absolute inset-x-3 bottom-3">
                <CookieBannerInner onAccept={() => toast.success("Cookies приняты")} />
              </div>
            </div>
            {!showCookies && (
              <button onClick={() => setShowCookies(true)} className="mt-4 text-xs text-brand-blue font-semibold">
                Показать живой баннер снова
              </button>
            )}
          </div>
        </Section>

        {/* 4. ERROR PAGES */}
        <Section number="04" title="Error Pages" subtitle="Страницы ошибок 404 и 500">
          <div className="grid gap-6 lg:grid-cols-2">
            <ErrorPagePreview type="404" />
            <ErrorPagePreview type="500" />
          </div>
        </Section>

        {/* 5. LOADING STATES */}
        <Section number="05" title="Loading States" subtitle="Скелетоны и спиннеры">
          <div className="space-y-6">
            <div>
              <p className="text-sm font-semibold text-muted-foreground mb-3">Skeleton МФО-карточки</p>
              <div className="grid gap-4 md:grid-cols-3">
                {[0, 1, 2].map((i) => <MFOSkeleton key={i} />)}
              </div>
            </div>
            <div>
              <p className="text-sm font-semibold text-muted-foreground mb-3">Кнопка с лоадером</p>
              <div className="flex gap-3 flex-wrap">
                <button
                  onClick={() => { setLoading(true); setTimeout(() => setLoading(false), 2000); }}
                  disabled={loading}
                  className="inline-flex items-center gap-2 rounded-2xl bg-brand-green px-6 py-3 text-sm font-bold text-white shadow-md hover:opacity-90 transition-opacity disabled:opacity-70"
                >
                  {loading && <Loader2 className="h-4 w-4 animate-spin" />}
                  {loading ? "Загрузка..." : "Получить деньги"}
                </button>
                <button
                  disabled
                  className="inline-flex items-center gap-2 rounded-2xl bg-foreground px-6 py-3 text-sm font-bold text-background opacity-70"
                >
                  <Loader2 className="h-4 w-4 animate-spin" /> Обработка
                </button>
              </div>
            </div>
          </div>
        </Section>

        {/* 6. EMPTY STATES */}
        <Section number="06" title="Empty States" subtitle="Пустые состояния">
          <div className="grid gap-6 md:grid-cols-2">
            <EmptyState
              icon={Search}
              title="Ничего не найдено"
              description="Попробуйте изменить запрос или сбросить фильтры — мы найдём подходящее МФО."
              actionLabel="Сбросить фильтры"
            />
            <EmptyState
              icon={Inbox}
              title="Список пуст"
              description="Здесь пока ничего нет. Добавьте первую заявку, чтобы начать работу."
              actionLabel="Создать заявку"
            />
          </div>
        </Section>

        {/* 7. TOASTS */}
        <Section number="07" title="Notifications / Toasts" subtitle="Всплывающие уведомления (Sonner)">
          <div className="rounded-3xl border border-border bg-card p-8">
            <p className="text-sm text-muted-foreground mb-5">Кликните для теста — toast выезжает справа:</p>
            <div className="flex flex-wrap gap-3">
              <button
                onClick={() => toast.success("Заявка успешно отправлена!", { description: "Мы свяжемся с вами в течение 5 минут" })}
                className="inline-flex items-center gap-2 rounded-xl bg-brand-green px-5 py-2.5 text-sm font-bold text-white"
              >
                <CheckCircle2 className="h-4 w-4" /> Success
              </button>
              <button
                onClick={() => toast.error("Что-то пошло не так", { description: "Попробуйте ещё раз через минуту" })}
                className="inline-flex items-center gap-2 rounded-xl bg-destructive px-5 py-2.5 text-sm font-bold text-destructive-foreground"
              >
                <XCircle className="h-4 w-4" /> Error
              </button>
              <button
                onClick={() => toast.warning("Проверьте данные", { description: "Поле email заполнено некорректно" })}
                className="inline-flex items-center gap-2 rounded-xl bg-brand-amber px-5 py-2.5 text-sm font-bold text-white"
              >
                <AlertTriangle className="h-4 w-4" /> Warning
              </button>
              <button
                onClick={() => toast.info("Обновлены условия", { description: "У 3 МФО изменились ставки" })}
                className="inline-flex items-center gap-2 rounded-xl bg-brand-blue px-5 py-2.5 text-sm font-bold text-white"
              >
                <Info className="h-4 w-4" /> Info
              </button>
            </div>

            {/* Visual previews */}
            <div className="mt-8 grid gap-3 md:grid-cols-2">
              <ToastPreview tone="green" icon={CheckCircle2} title="Success" text="Заявка отправлена" />
              <ToastPreview tone="red" icon={XCircle} title="Error" text="Не удалось загрузить" />
              <ToastPreview tone="amber" icon={AlertTriangle} title="Warning" text="Проверьте поле email" />
              <ToastPreview tone="blue" icon={Info} title="Info" text="Доступно обновление" />
            </div>
          </div>
        </Section>

        {/* 8. BACK TO TOP */}
        <Section number="08" title="Back-to-Top" subtitle="Кнопка возврата наверх">
          <div className="rounded-3xl border border-border bg-card p-8">
            <p className="text-sm text-muted-foreground">
              Скрольте страницу вниз — после 400px справа внизу появится зелёная круглая кнопка со стрелкой.
              {showBackTop ? " ✅ Сейчас она видна." : " Сейчас скрыта."}
            </p>
            <div className="mt-5 inline-flex items-center justify-center h-14 w-14 rounded-full bg-brand-green text-white shadow-lg shadow-brand-green/30">
              <ArrowUp className="h-6 w-6" />
            </div>
          </div>
        </Section>
      </main>

      {/* Live floating elements */}
      {showCookies && <CookieBanner onAccept={() => setShowCookies(false)} onSettings={() => toast.info("Открыты настройки cookies")} />}
      {showBackTop && (
        <button
          onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
          aria-label="Наверх"
          className="fixed bottom-6 right-6 z-40 inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-green text-white shadow-xl shadow-brand-green/40 hover:scale-110 transition-transform"
        >
          <ArrowUp className="h-6 w-6" />
        </button>
      )}

      <SiteFooter />
    </div>
  );
}

/* ───────── Section wrapper ───────── */

function Section({ number, title, subtitle, children }: { number: string; title: string; subtitle: string; children: React.ReactNode }) {
  return (
    <section>
      <div className="flex items-baseline gap-4 mb-6">
        <span className="text-5xl font-extrabold text-muted-foreground/30 tabular-nums">{number}</span>
        <div>
          <h2 className="text-2xl md:text-3xl font-extrabold tracking-tight text-foreground">{title}</h2>
          <p className="text-sm text-muted-foreground mt-0.5">{subtitle}</p>
        </div>
      </div>
      {children}
    </section>
  );
}

/* ───────── 1. Contact Form ───────── */

function ContactForm() {
  return (
    <form
      onSubmit={(e) => { e.preventDefault(); toast.success("Сообщение отправлено!"); }}
      className="rounded-3xl border border-border bg-card p-8 md:p-10 shadow-lg max-w-2xl"
    >
      <h3 className="text-2xl font-bold text-foreground">Свяжитесь с нами</h3>
      <p className="mt-1.5 text-sm text-muted-foreground">Ответим в течение 1 рабочего дня</p>

      <div className="mt-7 space-y-5">
        <Field label="Имя">
          <input required maxLength={100} placeholder="Ваше имя"
            className="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20" />
        </Field>
        <Field label="Email">
          <input required type="email" maxLength={255} placeholder="you@example.com"
            className="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20" />
        </Field>
        <Field label="Тема">
          <select required defaultValue=""
            className="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
            <option value="" disabled>Выберите тему</option>
            <option>Вопрос по займу</option>
            <option>Сотрудничество</option>
            <option>Жалоба</option>
            <option>Другое</option>
          </select>
        </Field>
        <Field label="Сообщение">
          <textarea required maxLength={1000} rows={5} placeholder="Расскажите подробнее..."
            className="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20 resize-none" />
        </Field>
      </div>

      <button type="submit"
        className="mt-7 w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-foreground px-6 py-4 text-base font-bold text-background hover:opacity-90 transition-opacity">
        Отправить <ArrowRight className="h-4 w-4" />
      </button>

      <p className="mt-4 text-xs text-muted-foreground text-center">
        Нажимая кнопку, вы соглашаетесь с{" "}
        <a href="#" className="text-brand-blue hover:underline">обработкой персональных данных</a>
      </p>
    </form>
  );
}

function Field({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <label className="block">
      <span className="block text-sm font-semibold text-foreground mb-1.5">{label}</span>
      {children}
    </label>
  );
}

/* ───────── 2. Newsletter Popup ───────── */

function NewsletterPopup({ onClose }: { onClose: () => void }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in" onClick={onClose}>
      <div onClick={(e) => e.stopPropagation()}
        className="relative w-full max-w-md rounded-3xl bg-card p-8 md:p-10 shadow-2xl animate-in zoom-in-95">
        <button onClick={onClose} aria-label="Закрыть"
          className="absolute top-4 right-4 inline-flex h-9 w-9 items-center justify-center rounded-full text-muted-foreground hover:bg-muted transition-colors">
          <X className="h-5 w-5" />
        </button>

        <div className="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-blue to-brand-green text-white shadow-lg">
          <Mail className="h-7 w-7" />
        </div>

        <h3 className="mt-5 text-2xl font-extrabold text-foreground leading-tight">
          Получайте лучшие предложения МФО на email
        </h3>
        <p className="mt-2 text-sm text-muted-foreground">
          Раз в неделю присылаем подборку акций, новых МФО и эксклюзивных промокодов.
        </p>

        <form onSubmit={(e) => { e.preventDefault(); toast.success("Подписка оформлена!"); onClose(); }}
          className="mt-6 space-y-3">
          <input required type="email" maxLength={255} placeholder="your@email.com"
            className="w-full rounded-xl border border-border bg-background px-4 py-3.5 text-sm focus:border-brand-green focus:outline-none focus:ring-2 focus:ring-brand-green/20" />
          <button type="submit"
            className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-brand-green px-6 py-3.5 text-sm font-bold text-white shadow-md hover:opacity-90 transition-opacity">
            Подписаться <ArrowRight className="h-4 w-4" />
          </button>
        </form>

        <p className="mt-4 text-xs text-muted-foreground text-center">
          Никакого спама. Отписаться можно в один клик. Согласие на{" "}
          <a href="#" className="text-brand-blue hover:underline">обработку данных</a>.
        </p>
      </div>
    </div>
  );
}

/* ───────── 3. Cookie Banner ───────── */

function CookieBanner({ onAccept, onSettings }: { onAccept: () => void; onSettings: () => void }) {
  return (
    <div className="fixed inset-x-3 bottom-3 z-40 md:inset-x-6 md:bottom-6 animate-in slide-in-from-bottom">
      <CookieBannerInner onAccept={onAccept} onSettings={onSettings} />
    </div>
  );
}

function CookieBannerInner({ onAccept, onSettings }: { onAccept?: () => void; onSettings?: () => void }) {
  return (
    <div className="rounded-2xl border border-border bg-card/95 backdrop-blur p-4 md:p-5 shadow-2xl flex flex-col md:flex-row md:items-center gap-4">
      <div className="flex items-start gap-3 flex-1">
        <div className="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-amber/15 text-brand-amber">
          <Cookie className="h-5 w-5" />
        </div>
        <div className="text-sm text-foreground">
          <span className="font-semibold">Мы используем cookies</span>
          <p className="text-muted-foreground text-xs md:text-sm mt-0.5">
            Чтобы улучшить работу сайта и показывать актуальные предложения МФО.{" "}
            <a href="#" className="text-brand-blue hover:underline">Подробнее</a>
          </p>
        </div>
      </div>
      <div className="flex gap-2 shrink-0">
        <button onClick={onSettings}
          className="rounded-xl border border-border bg-background px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted transition-colors">
          Настройки
        </button>
        <button onClick={onAccept}
          className="rounded-xl bg-brand-green px-5 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-90 transition-opacity">
          Принять
        </button>
      </div>
    </div>
  );
}

/* ───────── 4. Error Pages ───────── */

function ErrorPagePreview({ type }: { type: "404" | "500" }) {
  const is404 = type === "404";
  return (
    <div className="rounded-3xl border border-border bg-card overflow-hidden">
      <div className={cn(
        "p-10 text-center",
        is404 ? "bg-gradient-to-br from-brand-blue/10 to-brand-green/10" : "bg-gradient-to-br from-destructive/10 to-brand-amber/10"
      )}>
        <div className={cn(
          "mx-auto inline-flex h-20 w-20 items-center justify-center rounded-3xl text-white shadow-lg",
          is404 ? "bg-gradient-to-br from-brand-blue to-brand-green" : "bg-gradient-to-br from-destructive to-brand-amber"
        )}>
          {is404 ? <FileQuestion className="h-10 w-10" /> : <ServerCrash className="h-10 w-10" />}
        </div>
        <div className="mt-5 text-7xl font-extrabold tracking-tight text-foreground/80 tabular-nums">{type}</div>
        <h3 className="mt-2 text-xl font-bold text-foreground">
          {is404 ? "Страница не найдена" : "Что-то пошло не так"}
        </h3>
        <p className="mt-2 text-sm text-muted-foreground max-w-sm mx-auto">
          {is404
            ? "Кажется, такой страницы нет или она была перенесена. Попробуйте поиск или популярные разделы."
            : "Произошла ошибка на сервере. Мы уже работаем над её устранением — попробуйте обновить страницу."}
        </p>

        {is404 && (
          <div className="mt-6 max-w-sm mx-auto relative">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <input placeholder="Поиск по сайту..."
              className="w-full rounded-xl border border-border bg-background pl-10 pr-3 py-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20" />
          </div>
        )}

        <div className="mt-6 flex flex-wrap justify-center gap-2">
          {(is404 ? ["Главная", "Каталог МФО", "Блог", "Контакты"] : ["Обновить", "На главную", "Поддержка"]).map((l) => (
            <Link key={l} to="/"
              className="rounded-full border border-border bg-background px-4 py-1.5 text-xs font-semibold text-foreground hover:bg-muted transition-colors">
              {l}
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}

/* ───────── 5. Skeletons ───────── */

function MFOSkeleton() {
  return (
    <div className="rounded-3xl border border-border bg-card p-6 animate-pulse">
      <div className="flex items-center gap-3">
        <div className="h-12 w-12 rounded-2xl bg-muted" />
        <div className="flex-1 space-y-2">
          <div className="h-4 w-2/3 rounded bg-muted" />
          <div className="h-3 w-1/3 rounded bg-muted" />
        </div>
      </div>
      <div className="mt-5 space-y-2">
        <div className="h-3 w-full rounded bg-muted" />
        <div className="h-3 w-5/6 rounded bg-muted" />
      </div>
      <div className="mt-5 flex gap-2">
        <div className="h-7 w-20 rounded-full bg-muted" />
        <div className="h-7 w-16 rounded-full bg-muted" />
      </div>
      <div className="mt-5 h-11 rounded-2xl bg-muted" />
    </div>
  );
}

/* ───────── 6. Empty State ───────── */

function EmptyState({ icon: Icon, title, description, actionLabel }: { icon: typeof Search; title: string; description: string; actionLabel: string }) {
  return (
    <div className="rounded-3xl border border-dashed border-border bg-card p-10 text-center">
      <div className="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-muted text-muted-foreground">
        <Icon className="h-8 w-8" />
      </div>
      <h3 className="mt-5 text-lg font-bold text-foreground">{title}</h3>
      <p className="mt-2 text-sm text-muted-foreground max-w-sm mx-auto">{description}</p>
      <button className="mt-5 inline-flex items-center gap-2 rounded-xl bg-foreground px-5 py-2.5 text-sm font-bold text-background hover:opacity-90 transition-opacity">
        {actionLabel}
      </button>
    </div>
  );
}

/* ───────── 7. Toast Preview ───────── */

const toneMap = {
  green: { bg: "bg-brand-green/10", border: "border-brand-green/30", icon: "text-brand-green" },
  red: { bg: "bg-destructive/10", border: "border-destructive/30", icon: "text-destructive" },
  amber: { bg: "bg-brand-amber/10", border: "border-brand-amber/30", icon: "text-brand-amber" },
  blue: { bg: "bg-brand-blue/10", border: "border-brand-blue/30", icon: "text-brand-blue" },
};

function ToastPreview({ tone, icon: Icon, title, text }: { tone: keyof typeof toneMap; icon: typeof CheckCircle2; title: string; text: string }) {
  const t = toneMap[tone];
  return (
    <div className={cn("flex items-start gap-3 rounded-2xl border p-4 shadow-sm", t.bg, t.border)}>
      <Icon className={cn("h-5 w-5 mt-0.5 shrink-0", t.icon)} />
      <div className="flex-1">
        <div className="text-sm font-bold text-foreground">{title}</div>
        <div className="text-xs text-muted-foreground mt-0.5">{text}</div>
      </div>
      <button className="text-muted-foreground hover:text-foreground"><X className="h-4 w-4" /></button>
    </div>
  );
}
