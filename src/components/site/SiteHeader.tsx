import { useState } from "react";
import { Link } from "@tanstack/react-router";
import { Search, Menu, X, ChevronDown, MapPin, Wallet, Sparkles } from "lucide-react";
import { cn } from "@/lib/utils";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";

type DropKey = "picks" | "amount" | "cities";

const picks: { label: string; slug: string }[] = [
  { label: "Без отказа", slug: "bez-otkaza" },
  { label: "С плохой КИ", slug: "s-plohoy-ki" },
  { label: "Пенсионерам", slug: "pensioneram" },
  { label: "Студентам", slug: "studentam" },
  { label: "Без справок", slug: "bez-spravok" },
  { label: "Срочно", slug: "srochno" },
];

const amounts: { label: string; slug: string }[] = [
  { label: "1 000 ₽", slug: "zaim-1000" },
  { label: "3 000 ₽", slug: "zaim-3000" },
  { label: "5 000 ₽", slug: "zaim-5000" },
  { label: "10 000 ₽", slug: "zaim-10000" },
  { label: "15 000 ₽", slug: "zaim-15000" },
  { label: "20 000 ₽", slug: "zaim-20000" },
  { label: "30 000 ₽", slug: "zaim-30000" },
  { label: "50 000 ₽", slug: "zaim-50000" },
  { label: "100 000 ₽", slug: "zaim-100000" },
];

const cities: { label: string; slug: string }[] = [
  { label: "Москва", slug: "moskva" },
  { label: "Санкт-Петербург", slug: "spb" },
  { label: "Казань", slug: "kazan" },
  { label: "Новосибирск", slug: "novosibirsk" },
  { label: "Екатеринбург", slug: "ekaterinburg" },
];

function Logo() {
  return (
    <Link to="/" className="flex items-center gap-1 select-none" aria-label="Zaymi Online">
      <span className="text-2xl font-extrabold tracking-tight text-brand-blue">
        Zaymi
      </span>
      <span className="text-2xl font-extrabold tracking-tight text-brand-green">
        Online
      </span>
    </Link>
  );
}

const navLinkBase =
  "inline-flex items-center gap-1 rounded-md px-3.5 py-2 text-sm font-semibold transition-colors duration-200";
const navLinkInactive = "text-brand-ink hover:bg-brand-blue/8 hover:text-brand-blue";
const navLinkActive = "bg-[#ecfdf5] text-[#047857]";

function NavLinkItem({ to, label }: { to: string; label: string }) {
  return (
    <Link
      to={to}
      className={cn(navLinkBase, navLinkInactive)}
      activeProps={{ className: cn(navLinkBase, navLinkActive) }}
      activeOptions={{ exact: to === "/" }}
    >
      {label}
    </Link>
  );
}

function NavTrigger({
  label,
  open,
}: {
  label: string;
  open: boolean;
}) {
  return (
    <button
      type="button"
      className={cn(navLinkBase, navLinkInactive)}
    >
      {label}
      <ChevronDown
        className={cn("h-3.5 w-3.5 transition-transform duration-200", open && "rotate-180")}
        strokeWidth={2.5}
      />
    </button>
  );
}

interface DropdownProps {
  open: boolean;
  children: React.ReactNode;
  width?: string;
}
function Dropdown({ open, children, width = "w-64" }: DropdownProps) {
  return (
    <div
      className={cn(
        "absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 transition-all duration-200",
        width,
        open
          ? "pointer-events-auto opacity-100 translate-y-0"
          : "pointer-events-none opacity-0 -translate-y-1",
      )}
    >
      <div className="rounded-lg border border-brand-line bg-white p-2 shadow-hover">
        {children}
      </div>
    </div>
  );
}

interface SiteHeaderProps {
  variant?: "auto" | "desktop" | "mobile";
}

export function SiteHeader({ variant = "auto" }: SiteHeaderProps) {
  const [openDrop, setOpenDrop] = useState<DropKey | null>(null);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [mobileSearchOpen, setMobileSearchOpen] = useState(false);

  const showMobile =
    variant === "mobile" ? "flex" : variant === "desktop" ? "hidden" : "flex md:hidden";
  const showDesktop =
    variant === "desktop" ? "flex" : variant === "mobile" ? "hidden" : "hidden md:flex";
  const desktopHeight =
    variant === "desktop" ? "h-[72px] px-6" : variant === "mobile" ? "h-16 px-4" : "h-16 px-4 md:h-[72px] md:px-6";

  return (
    <header className="sticky top-0 z-40 border-b border-brand-line bg-white/85 backdrop-blur-lg shadow-sticky">
      <div className={cn("mx-auto flex max-w-7xl items-center gap-6", desktopHeight)}>
        <div className={showMobile}>
          <Sheet open={mobileOpen} onOpenChange={setMobileOpen}>
            <SheetTrigger asChild>
              <button
                aria-label="Открыть меню"
                className="-ml-1 flex h-10 w-10 items-center justify-center rounded-md text-brand-ink hover:bg-brand-soft"
              >
                <Menu className="h-6 w-6" strokeWidth={2.25} />
              </button>
            </SheetTrigger>
            <SheetContent
              side="right"
              className="w-[88vw] max-w-sm border-l border-brand-line bg-white p-0"
            >
              <MobileMenu onClose={() => setMobileOpen(false)} />
            </SheetContent>
          </Sheet>
        </div>

        <div
          className={cn(
            "flex flex-1 items-center",
            variant === "mobile"
              ? "justify-center"
              : variant === "desktop"
                ? "flex-none w-[180px] justify-start"
                : "justify-center md:flex-none md:w-[180px] md:justify-start",
          )}
        >
          <Logo />
        </div>

        <nav
          className={cn("flex-1 items-center justify-center gap-1", showDesktop)}
          onMouseLeave={() => setOpenDrop(null)}
        >
          <NavLinkItem to="/" label="Главная" />
          <NavLinkItem to="/mfo" label="Каталог МФО" />

          <div className="relative" onMouseEnter={() => setOpenDrop("picks")}>
            <NavTrigger label="Подборки" open={openDrop === "picks"} />
            <Dropdown open={openDrop === "picks"} width="w-60">
              {picks.map((p) => (
                <Link
                  key={p.slug}
                  to="/situations/$slug"
                  params={{ slug: p.slug }}
                  className="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-semibold text-brand-ink transition-colors hover:bg-brand-soft hover:text-brand-blue"
                >
                  <Sparkles className="h-4 w-4 text-brand-muted" strokeWidth={2.25} />
                  {p.label}
                </Link>
              ))}
            </Dropdown>
          </div>

          <div className="relative" onMouseEnter={() => setOpenDrop("amount")}>
            <NavTrigger label="По сумме" open={openDrop === "amount"} />
            <Dropdown open={openDrop === "amount"} width="w-72">
              <div className="grid grid-cols-3 gap-1">
                {amounts.map((a) => (
                  <Link
                    key={a.slug}
                    to="/summa/$slug"
                    params={{ slug: a.slug }}
                    className="rounded-md px-2 py-2 text-center text-sm font-bold text-brand-ink transition-colors hover:bg-brand-green/10 hover:text-brand-green"
                  >
                    {a.label}
                  </Link>
                ))}
              </div>
            </Dropdown>
          </div>

          <div className="relative" onMouseEnter={() => setOpenDrop("cities")}>
            <NavTrigger label="По городам" open={openDrop === "cities"} />
            <Dropdown open={openDrop === "cities"} width="w-64">
              {cities.map((c) => (
                <Link
                  key={c.slug}
                  to="/goroda/$slug"
                  params={{ slug: c.slug }}
                  className="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-semibold text-brand-ink transition-colors hover:bg-brand-soft hover:text-brand-blue"
                >
                  <MapPin className="h-4 w-4 text-brand-muted" strokeWidth={2.25} />
                  {c.label}
                </Link>
              ))}
            </Dropdown>
          </div>

          <NavLinkItem to="/blog" label="Блог" />
        </nav>

        <form
          className={cn(
            "h-11 w-[330px] items-center gap-1 rounded-pill border border-brand-line bg-white pl-4 pr-1 transition-all focus-within:border-brand-blue focus-within:shadow-card",
            showDesktop,
          )}
          onSubmit={(e) => e.preventDefault()}
        >
          <Search className="h-4 w-4 shrink-0 text-brand-muted" strokeWidth={2.25} />
          <input
            type="search"
            placeholder="Поиск МФО, статей..."
            className="min-w-0 flex-1 bg-transparent text-sm font-medium text-brand-ink placeholder:text-brand-muted focus:outline-none"
            aria-label="Поиск"
          />
          <button
            type="submit"
            className="h-9 shrink-0 rounded-pill bg-brand-green px-4 text-sm font-bold text-white shadow-card transition-all hover:bg-brand-green/90 hover:shadow-hover active:scale-[0.97]"
          >
            Найти
          </button>
        </form>

        <div className={showMobile}>
          <button
            aria-label="Поиск"
            onClick={() => setMobileSearchOpen((v) => !v)}
            className="-mr-1 flex h-10 w-10 items-center justify-center rounded-md text-brand-ink hover:bg-brand-soft"
          >
            <Search className="h-5 w-5" strokeWidth={2.25} />
          </button>
        </div>
      </div>

      {mobileSearchOpen && (
        <div
          className={cn(
            "border-t border-brand-line bg-white px-4 py-3 animate-in fade-in slide-in-from-top-1 duration-200",
            variant === "desktop" ? "hidden" : variant === "mobile" ? "block" : "md:hidden",
          )}
        >
          <form
            className="flex h-11 items-center gap-1 rounded-pill border border-brand-line bg-white pl-4 pr-1 focus-within:border-brand-blue"
            onSubmit={(e) => e.preventDefault()}
          >
            <Search className="h-4 w-4 shrink-0 text-brand-muted" strokeWidth={2.25} />
            <input
              autoFocus
              type="search"
              placeholder="Поиск МФО, статей..."
              className="min-w-0 flex-1 bg-transparent text-sm font-medium text-brand-ink placeholder:text-brand-muted focus:outline-none"
            />
            <button
              type="submit"
              className="h-9 shrink-0 rounded-pill bg-brand-green px-4 text-sm font-bold text-white"
            >
              Найти
            </button>
          </form>
        </div>
      )}
    </header>
  );
}

function MobileMenu({ onClose }: { onClose: () => void }) {
  const [openSection, setOpenSection] = useState<DropKey | null>("picks");
  const toggle = (k: DropKey) =>
    setOpenSection((cur) => (cur === k ? null : k));

  return (
    <div className="flex h-full flex-col">
      <div className="flex items-center justify-between border-b border-brand-line px-5 py-4">
        <Logo />
        <button
          onClick={onClose}
          aria-label="Закрыть меню"
          className="flex h-10 w-10 items-center justify-center rounded-md text-brand-ink hover:bg-brand-soft"
        >
          <X className="h-5 w-5" strokeWidth={2.25} />
        </button>
      </div>

      <nav className="flex-1 overflow-y-auto px-3 py-4">
        <Link
          to="/"
          onClick={onClose}
          className="flex items-center justify-between rounded-md px-4 py-3.5 text-base font-bold text-brand-ink hover:bg-brand-soft"
          activeProps={{ className: "flex items-center justify-between rounded-md bg-[#ecfdf5] px-4 py-3.5 text-base font-bold text-[#047857]" }}
          activeOptions={{ exact: true }}
        >
          Главная
        </Link>
        <Link
          to="/mfo"
          onClick={onClose}
          className="mt-1 flex items-center justify-between rounded-md px-4 py-3.5 text-base font-bold text-brand-ink hover:bg-brand-soft"
        >
          Каталог МФО
        </Link>

        <MobileSection
          label="Подборки"
          icon={Sparkles}
          open={openSection === "picks"}
          onToggle={() => toggle("picks")}
        >
          {picks.map((p) => (
            <Link
              key={p.slug}
              to="/situations/$slug"
              params={{ slug: p.slug }}
              onClick={onClose}
              className="block rounded-md px-4 py-2.5 text-sm font-semibold text-brand-muted hover:bg-brand-soft hover:text-brand-blue"
            >
              {p.label}
            </Link>
          ))}
        </MobileSection>

        <MobileSection
          label="По сумме"
          icon={Wallet}
          open={openSection === "amount"}
          onToggle={() => toggle("amount")}
        >
          <div className="grid grid-cols-3 gap-1.5 px-2 py-1">
            {amounts.map((a) => (
              <Link
                key={a.slug}
                to="/summa/$slug"
                params={{ slug: a.slug }}
                onClick={onClose}
                className="rounded-md bg-brand-soft px-2 py-2 text-center text-sm font-bold text-brand-ink hover:bg-brand-green/10 hover:text-brand-green"
              >
                {a.label}
              </Link>
            ))}
          </div>
        </MobileSection>

        <MobileSection
          label="По городам"
          icon={MapPin}
          open={openSection === "cities"}
          onToggle={() => toggle("cities")}
        >
          {cities.map((c) => (
            <Link
              key={c.slug}
              to="/goroda/$slug"
              params={{ slug: c.slug }}
              onClick={onClose}
              className="block rounded-md px-4 py-2.5 text-sm font-semibold text-brand-muted hover:bg-brand-soft hover:text-brand-blue"
            >
              {c.label}
            </Link>
          ))}
        </MobileSection>

        <Link
          to="/blog"
          onClick={onClose}
          className="mt-1 flex items-center justify-between rounded-md px-4 py-3.5 text-base font-bold text-brand-ink hover:bg-brand-soft"
        >
          Блог
        </Link>
      </nav>

      <div className="border-t border-brand-line p-4">
        <button className="h-12 w-full rounded-md bg-brand-green text-base font-bold text-white shadow-card hover:bg-brand-green/90 active:scale-[0.98] transition-all">
          Получить займ
        </button>
      </div>
    </div>
  );
}

function MobileSection({
  label,
  icon: Icon,
  open,
  onToggle,
  children,
}: {
  label: string;
  icon: typeof Sparkles;
  open: boolean;
  onToggle: () => void;
  children: React.ReactNode;
}) {
  return (
    <div className="mt-1">
      <button
        onClick={onToggle}
        className="flex w-full items-center justify-between rounded-md px-4 py-3.5 text-base font-bold text-brand-ink hover:bg-brand-soft"
      >
        <span className="flex items-center gap-2.5">
          <Icon className="h-4 w-4 text-brand-muted" strokeWidth={2.25} />
          {label}
        </span>
        <ChevronDown
          className={cn("h-4 w-4 text-brand-muted transition-transform", open && "rotate-180")}
          strokeWidth={2.5}
        />
      </button>
      {open && (
        <div className="mt-1 mb-2 ml-2 border-l-2 border-brand-line pl-2 animate-in fade-in slide-in-from-top-1 duration-200">
          {children}
        </div>
      )}
    </div>
  );
}
