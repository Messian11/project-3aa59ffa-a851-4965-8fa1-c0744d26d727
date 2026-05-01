import { createFileRoute } from "@tanstack/react-router";
import { useState } from "react";
import { Section } from "@/components/styleguide/Section";
import { ColorSwatch } from "@/components/styleguide/ColorSwatch";
import { MfoButton } from "@/components/styleguide/MfoButton";
import { MfoBadge } from "@/components/styleguide/MfoBadge";
import { MfoCard } from "@/components/styleguide/MfoCard";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Slider } from "@/components/ui/slider";

export const Route = createFileRoute("/styleguide")({
  component: StyleGuide,
});

const colors = [
  { name: "Primary Blue", hex: "#2563eb", description: "Доверие, навигация, ссылки", textOn: "dark" as const },
  { name: "Accent Green", hex: "#10b981", description: "CTA-кнопки, деньги, успех", textOn: "dark" as const },
  { name: "Warning Amber", hex: "#f59e0b", description: "Спецпредложения, «0% первый займ»", textOn: "dark" as const },
  { name: "Ink", hex: "#172033", description: "Основной текст", textOn: "dark" as const },
  { name: "Muted", hex: "#647084", description: "Вторичный текст", textOn: "dark" as const },
  { name: "Line", hex: "#e5edf5", description: "Границы, разделители" },
  { name: "Soft", hex: "#f6fbff", description: "Фоны, мягкие карточки" },
  { name: "White", hex: "#ffffff", description: "Базовый фон" },
  { name: "Dark", hex: "#0f172a", description: "Футер, акценты", textOn: "dark" as const },
];

const navLinks = [
  { id: "colors", label: "Цвета" },
  { id: "typography", label: "Типографика" },
  { id: "buttons", label: "Кнопки" },
  { id: "forms", label: "Формы" },
  { id: "cards", label: "Карточки" },
  { id: "badges", label: "Бэйджи" },
  { id: "spacing", label: "Отступы" },
];

const spacingScale = [4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 100];

function StyleGuide() {
  const [amount, setAmount] = useState([15000]);

  return (
    <div className="min-h-screen bg-page-gradient">
      {/* Sticky header */}
      <header className="sticky top-0 z-40 border-b border-brand-line bg-white/80 backdrop-blur-md shadow-sticky">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
          <div className="flex items-center gap-2">
            <div className="flex h-9 w-9 items-center justify-center rounded-md bg-gradient-to-br from-brand-blue to-brand-green text-white font-extrabold">
              М
            </div>
            <div>
              <div className="text-sm font-extrabold text-brand-ink leading-none">МФО Гид</div>
              <div className="text-[11px] text-brand-muted">Дизайн-система v1.0</div>
            </div>
          </div>
          <nav className="hidden md:flex items-center gap-1">
            {navLinks.map((l) => (
              <a
                key={l.id}
                href={`#${l.id}`}
                className="rounded-md px-3 py-1.5 text-sm font-semibold text-brand-muted transition hover:bg-brand-soft hover:text-brand-blue"
              >
                {l.label}
              </a>
            ))}
          </nav>
          <MfoButton size="sm" variant="primary" className="hidden sm:inline-flex">
            Получить займ
          </MfoButton>
        </div>
      </header>

      {/* Hero */}
      <div className="bg-hero-gradient">
        <div className="mx-auto max-w-7xl px-5 pt-16 pb-20 md:pt-24 md:pb-28">
          <MfoBadge tone="info" className="mb-5">
            <span className="h-1.5 w-1.5 rounded-full bg-brand-blue" />
            Style Guide · Russian MFO Aggregator
          </MfoBadge>
          <h1
            className="font-extrabold tracking-tight text-brand-ink"
            style={{ fontSize: "clamp(34px, 5vw, 56px)", lineHeight: 1.05 }}
          >
            Дизайн-система <br className="hidden sm:block" />
            <span className="bg-gradient-to-r from-brand-blue to-brand-green bg-clip-text text-transparent">
              быстрого финтеха
            </span>
          </h1>
          <p className="mt-5 max-w-2xl text-lg text-brand-muted">
            Цвета, типографика и компоненты для агрегатора займов. Mobile-first, доступно,
            проверено на максимальную скорость восприятия.
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <MfoButton variant="primary" size="lg">Оформить займ за 5 минут</MfoButton>
            <MfoButton variant="outline" size="lg">Посмотреть компоненты</MfoButton>
          </div>
          <div className="mt-10 flex flex-wrap items-center gap-2">
            <MfoBadge tone="warning">⚡ Первый займ 0%</MfoBadge>
            <MfoBadge tone="success">✓ Без отказа</MfoBadge>
            <MfoBadge tone="info">24/7 онлайн</MfoBadge>
          </div>
        </div>
      </div>

      <main className="mx-auto max-w-7xl px-5">
        {/* Colors */}
        <Section
          id="colors"
          eyebrow="01 — Палитра"
          title="Цветовая система"
          description="Все цвета бренда с HEX-кодами. Использование строго через семантические токены в styles.css."
        >
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            {colors.map((c) => (
              <ColorSwatch key={c.hex} {...c} />
            ))}
          </div>

          <div className="mt-10 grid gap-4 md:grid-cols-2">
            <div
              className="rounded-xl p-8"
              style={{
                background:
                  "radial-gradient(circle at 75% 15%, rgba(16,185,129,0.14), transparent 25%), radial-gradient(circle at 15% 18%, rgba(37,99,235,0.13), transparent 28%), linear-gradient(180deg, #f8fbff, #eefdf6 78%, #fff)",
              }}
            >
              <div className="text-xs font-bold uppercase tracking-wider text-brand-blue">
                Hero gradient
              </div>
              <div className="mt-2 text-xl font-extrabold text-brand-ink">
                Радиальные акценты на светлом фоне
              </div>
              <div className="mt-4 font-mono text-[11px] text-brand-muted">
                radial 75% 15% green · radial 15% 18% blue · linear 180°
              </div>
            </div>
            <div
              className="rounded-xl p-8"
              style={{
                background: "linear-gradient(180deg, #f7fbff 0, #fff 420px)",
                minHeight: 180,
              }}
            >
              <div className="text-xs font-bold uppercase tracking-wider text-brand-blue">
                Page background
              </div>
              <div className="mt-2 text-xl font-extrabold text-brand-ink">
                Лёгкий переход в белый
              </div>
              <div className="mt-4 font-mono text-[11px] text-brand-muted">
                linear-gradient(180deg, #f7fbff 0, #fff 420px)
              </div>
            </div>
          </div>
        </Section>

        {/* Typography */}
        <Section
          id="typography"
          eyebrow="02 — Шрифты"
          title="Типографика"
          description="Inter — единственный шрифт. 800 для заголовков, 500 для основного текста."
        >
          <MfoCard className="space-y-8">
            <div>
              <div className="mb-2 font-mono text-xs text-brand-muted">
                H1 · Inter 800 · clamp(34px, 5vw, 56px)
              </div>
              <h1
                className="font-extrabold text-brand-ink"
                style={{ fontSize: "clamp(34px, 5vw, 56px)", lineHeight: 1.05 }}
              >
                Заголовок главной страницы
              </h1>
            </div>
            <div className="border-t border-brand-line pt-6">
              <div className="mb-2 font-mono text-xs text-brand-muted">H2 · Inter 800 · 36px</div>
              <h2 className="text-[36px] font-extrabold leading-tight text-brand-ink">
                Подзаголовок раздела
              </h2>
            </div>
            <div className="border-t border-brand-line pt-6">
              <div className="mb-2 font-mono text-xs text-brand-muted">H3 · Inter 800 · 24px</div>
              <h3 className="text-2xl font-extrabold text-brand-ink">
                Заголовок карточки займа
              </h3>
            </div>
            <div className="border-t border-brand-line pt-6">
              <div className="mb-2 font-mono text-xs text-brand-muted">
                Body · Inter 500 · 16px
              </div>
              <p className="text-base font-medium text-brand-ink">
                Обычный текст. Получите займ до 100 000 рублей за 5 минут без отказа и
                поручителей. Деньги поступают на карту любого банка круглосуточно.
              </p>
            </div>
            <div className="border-t border-brand-line pt-6">
              <div className="mb-2 font-mono text-xs text-brand-muted">
                Muted · Inter 500 · 14px
              </div>
              <p className="text-sm font-medium text-brand-muted">
                Вторичный текст — для подсказок, мелких деталей и юридической информации.
              </p>
            </div>
          </MfoCard>
        </Section>

        {/* Buttons */}
        <Section
          id="buttons"
          eyebrow="03 — Действия"
          title="Кнопки"
          description="Зелёная — основной CTA. Синяя — навигация. Outline и ghost — вторичные действия."
        >
          <div className="grid gap-6 md:grid-cols-2">
            <MfoCard>
              <div className="mb-4 text-xs font-bold uppercase tracking-wider text-brand-muted">
                Primary · Green CTA
              </div>
              <div className="flex flex-wrap items-center gap-3">
                <MfoButton variant="primary" size="sm">Получить</MfoButton>
                <MfoButton variant="primary" size="md">Получить займ</MfoButton>
                <MfoButton variant="primary" size="lg">Оформить за 5 минут</MfoButton>
              </div>
              <div className="mt-3">
                <MfoButton variant="primary" disabled>Disabled</MfoButton>
              </div>
            </MfoCard>

            <MfoCard>
              <div className="mb-4 text-xs font-bold uppercase tracking-wider text-brand-muted">
                Secondary · Blue
              </div>
              <div className="flex flex-wrap items-center gap-3">
                <MfoButton variant="secondary" size="sm">Сравнить</MfoButton>
                <MfoButton variant="secondary" size="md">Все предложения</MfoButton>
                <MfoButton variant="secondary" size="lg">Подобрать займ</MfoButton>
              </div>
            </MfoCard>

            <MfoCard>
              <div className="mb-4 text-xs font-bold uppercase tracking-wider text-brand-muted">
                Outline
              </div>
              <div className="flex flex-wrap items-center gap-3">
                <MfoButton variant="outline" size="sm">Подробнее</MfoButton>
                <MfoButton variant="outline" size="md">Условия</MfoButton>
                <MfoButton variant="outline" size="lg">Узнать ставку</MfoButton>
              </div>
            </MfoCard>

            <MfoCard>
              <div className="mb-4 text-xs font-bold uppercase tracking-wider text-brand-muted">
                Ghost
              </div>
              <div className="flex flex-wrap items-center gap-3">
                <MfoButton variant="ghost" size="sm">Отмена</MfoButton>
                <MfoButton variant="ghost" size="md">Пропустить</MfoButton>
                <MfoButton variant="ghost" size="lg">Войти</MfoButton>
              </div>
            </MfoCard>
          </div>
        </Section>

        {/* Forms */}
        <Section
          id="forms"
          eyebrow="04 — Ввод"
          title="Элементы форм"
          description="Поля для калькулятора займа: сумма, срок, тип кредита."
        >
          <MfoCard className="space-y-7">
            <div className="grid gap-5 md:grid-cols-2">
              <div className="space-y-2">
                <Label htmlFor="phone" className="text-sm font-bold text-brand-ink">
                  Номер телефона
                </Label>
                <Input
                  id="phone"
                  type="tel"
                  placeholder="+7 (___) ___-__-__"
                  className="h-12 rounded-md border-brand-line text-base font-medium focus-visible:ring-brand-blue"
                />
                <p className="text-xs text-brand-muted">Для подтверждения заявки</p>
              </div>

              <div className="space-y-2">
                <Label htmlFor="city" className="text-sm font-bold text-brand-ink">
                  Город
                </Label>
                <Select>
                  <SelectTrigger
                    id="city"
                    className="h-12 rounded-md border-brand-line text-base font-medium focus:ring-brand-blue"
                  >
                    <SelectValue placeholder="Выберите город" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="msk">Москва</SelectItem>
                    <SelectItem value="spb">Санкт-Петербург</SelectItem>
                    <SelectItem value="ekb">Екатеринбург</SelectItem>
                    <SelectItem value="nsk">Новосибирск</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <div className="space-y-3 rounded-lg bg-brand-soft p-5">
              <div className="flex items-baseline justify-between">
                <Label className="text-sm font-bold text-brand-ink">Сумма займа</Label>
                <span className="text-2xl font-extrabold text-brand-green">
                  {amount[0].toLocaleString("ru-RU")} ₽
                </span>
              </div>
              <Slider
                value={amount}
                onValueChange={setAmount}
                min={1000}
                max={100000}
                step={1000}
                className="[&_[role=slider]]:h-5 [&_[role=slider]]:w-5 [&_[role=slider]]:border-2 [&_[role=slider]]:border-brand-green [&_[role=slider]]:bg-white"
              />
              <div className="flex justify-between text-xs font-semibold text-brand-muted">
                <span>1 000 ₽</span>
                <span>100 000 ₽</span>
              </div>
            </div>
          </MfoCard>
        </Section>

        {/* Cards */}
        <Section
          id="cards"
          eyebrow="05 — Контейнеры"
          title="Карточки"
          description="Три варианта для предложений МФО, рейтингов и информационных блоков."
        >
          <div className="grid gap-5 md:grid-cols-3">
            <MfoCard variant="default">
              <MfoBadge tone="info" className="mb-3">Default</MfoBadge>
              <h3 className="text-xl font-extrabold text-brand-ink">Webbankir</h3>
              <p className="mt-1 text-sm text-brand-muted">До 100 000 ₽ · 30 дней</p>
              <div className="mt-4 flex items-baseline gap-2">
                <span className="text-3xl font-extrabold text-brand-green">0%</span>
                <span className="text-sm text-brand-muted">первый займ</span>
              </div>
              <MfoButton variant="primary" className="mt-5 w-full">Оформить</MfoButton>
            </MfoCard>

            <MfoCard variant="elevated">
              <MfoBadge tone="warning" className="mb-3">⭐ Рекомендуем</MfoBadge>
              <h3 className="text-xl font-extrabold text-brand-ink">Займер</h3>
              <p className="mt-1 text-sm text-brand-muted">До 30 000 ₽ · 30 дней</p>
              <div className="mt-4 flex items-baseline gap-2">
                <span className="text-3xl font-extrabold text-brand-green">0,8%</span>
                <span className="text-sm text-brand-muted">в день</span>
              </div>
              <MfoButton variant="primary" className="mt-5 w-full">Оформить</MfoButton>
            </MfoCard>

            <MfoCard variant="outline">
              <MfoBadge tone="neutral" className="mb-3">Outline</MfoBadge>
              <h3 className="text-xl font-extrabold text-brand-ink">МигКредит</h3>
              <p className="mt-1 text-sm text-brand-muted">До 50 000 ₽ · 168 дней</p>
              <div className="mt-4 flex items-baseline gap-2">
                <span className="text-3xl font-extrabold text-brand-green">1%</span>
                <span className="text-sm text-brand-muted">в день</span>
              </div>
              <MfoButton variant="outline" className="mt-5 w-full">Подробнее</MfoButton>
            </MfoCard>
          </div>
        </Section>

        {/* Badges */}
        <Section
          id="badges"
          eyebrow="06 — Метки"
          title="Бэйджи"
          description="Pill-бэйджи для статусов, рекламных меток и состояний заявки."
        >
          <MfoCard>
            <div className="flex flex-wrap items-center gap-3">
              <MfoBadge tone="success">✓ Одобрено</MfoBadge>
              <MfoBadge tone="info">Новинка</MfoBadge>
              <MfoBadge tone="warning">Первый займ 0%</MfoBadge>
              <MfoBadge tone="danger">Просрочено</MfoBadge>
              <MfoBadge tone="neutral">В обработке</MfoBadge>
            </div>
            <div className="mt-6 grid gap-2 text-sm text-brand-muted sm:grid-cols-2">
              <div><strong className="text-brand-ink">Success</strong> — одобрение, успех</div>
              <div><strong className="text-brand-ink">Info</strong> — нейтральная информация</div>
              <div><strong className="text-brand-ink">Warning</strong> — спецпредложения</div>
              <div><strong className="text-brand-ink">Danger</strong> — ошибки, просрочки</div>
            </div>
          </MfoCard>
        </Section>

        {/* Spacing */}
        <Section
          id="spacing"
          eyebrow="07 — Сетка"
          title="Шкала отступов"
          description="Базовая шкала в пикселях. Используйте только эти значения для отступов и зазоров."
        >
          <MfoCard>
            <div className="space-y-3">
              {spacingScale.map((px) => (
                <div key={px} className="flex items-center gap-4">
                  <div className="w-14 text-right font-mono text-sm font-bold text-brand-ink">
                    {px}px
                  </div>
                  <div
                    className="h-6 rounded-sm bg-gradient-to-r from-brand-blue to-brand-green"
                    style={{ width: `${px}px` }}
                  />
                  <div className="text-xs text-brand-muted">
                    {px <= 16 ? "inline / icon gap" : px <= 32 ? "components" : "sections"}
                  </div>
                </div>
              ))}
            </div>
          </MfoCard>

          <div className="mt-6 grid gap-4 sm:grid-cols-3">
            <div className="rounded-lg border border-brand-line bg-white p-5">
              <div className="text-xs font-bold uppercase tracking-wider text-brand-muted">Тени</div>
              <div className="mt-3 h-16 rounded-md bg-white shadow-card" />
              <div className="mt-2 font-mono text-[11px] text-brand-muted">shadow-card</div>
            </div>
            <div className="rounded-lg border border-brand-line bg-white p-5">
              <div className="text-xs font-bold uppercase tracking-wider text-brand-muted">Hover</div>
              <div className="mt-3 h-16 rounded-md bg-white shadow-hover" />
              <div className="mt-2 font-mono text-[11px] text-brand-muted">shadow-hover</div>
            </div>
            <div className="rounded-lg border border-brand-line bg-white p-5">
              <div className="text-xs font-bold uppercase tracking-wider text-brand-muted">Header</div>
              <div className="mt-3 h-16 rounded-md bg-white shadow-sticky" />
              <div className="mt-2 font-mono text-[11px] text-brand-muted">shadow-sticky</div>
            </div>
          </div>
        </Section>
      </main>

      <footer className="mt-16 bg-brand-dark py-10 text-center">
        <div className="mx-auto max-w-7xl px-5">
          <div className="text-sm font-semibold text-white/80">
            МФО Гид · Дизайн-система v1.0
          </div>
          <div className="mt-1 text-xs text-white/50">
            Готово к переносу на WordPress · Mobile-first · WCAG AA
          </div>
        </div>
      </footer>
    </div>
  );
}
