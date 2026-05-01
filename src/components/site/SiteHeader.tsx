import { useState } from "react";
import { Link } from "@tanstack/react-router";
import { Search, Menu, X, ChevronDown, MapPin, Wallet, Sparkles } from "lucide-react";
import { cn } from "@/lib/utils";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";

type DropKey = "picks" | "amount" | "cities";

const picks = [
  "Без отказа",
  "С плохой КИ",
  "Пенсионерам",
  "Студентам",
  "Без справок",
  "Срочно",
];

const amounts = [
  "1 000 ₽",
  "3 000 ₽",
  "5 000 ₽",
  "10 000 ₽",
  "15 000 ₽",
  "20 000 ₽",
  "30 000 ₽",
  "50 000 ₽",
  "100 000 ₽",
];

const cities = [
  "Москва",
  "Санкт-Петербург",
  "Казань",
  "Новосибирск",
  "Екатеринбург",
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

interface NavLinkProps {
  label: string;
  active?: boolean;
  hasDropdown?: boolean;
  open?: boolean;
}
function NavLink({ label, active, hasDropdown, open }: NavLinkProps) {
  return (
    <button
      type="button"
      className={cn(
        "inline-flex items-center gap-1 rounded-md px-3.5 py-2 text-sm font-semibold transition-colors duration-200",
        active
          ? "bg-[#ecfdf5] text-[#047857]"
          : "text-brand-ink hover:bg-brand-blue/8 hover:text-brand-blue",
      )}
    >
      {label}
      {hasDropdown && (
        <ChevronDown
          className={cn(
            "h-3.5 w-3.5 transition-transform duration-200",
            open && "rotate-180",
          )}
          strokeWidth={2.5}
        />
      )}
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

function DropdownItem({
  children,
  icon: Icon,
}: {
  children: React.ReactNode;
  icon?: typeof Sparkles;
}) {
  return (
    <a
      href="#"
      className="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-semibold text-brand-ink transition-colors hover:bg-brand-soft hover:text-brand-blue"
    >
      {Icon && <Icon className="h-4 w-4 text-brand-muted" strokeWidth={2.25} />}
      {children}
    </a>
  );
}

interface SiteHeaderProps {
  variant?: "auto" | "desktop" | "mobile";
}

export function SiteHeader({ variant = "auto" }: SiteHeaderProps) {
  const [openDrop, setOpenDrop] = useState<DropKey | null>(null);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [mobileSearchOpen, setMobileSearchOpen] = useState(false);

  // Force-show / force-hide helpers that override responsive defaults
  const showMobile =
    variant === "mobile" ? "flex" : variant === "desktop" ? "hidden" : "flex md:hidden";
  const showDesktop =
    variant === "desktop" ? "flex" : variant === "mobile" ? "hidden" : "hidden md:flex";
  const desktopHeight =
    variant === "desktop" ? "h-[72px] px-6" : variant === "mobile" ? "h-16 px-4" : "h-16 px-4 md:h-[72px] md:px-6";

  return (
    <header className="sticky top-0 z-40 border-b border-brand-line bg-white/85 backdrop-blur-lg shadow-sticky">
      <div className={cn("mx-auto flex max-w-7xl items-center gap-6", desktopHeight)}>
        {/* Mobile: hamburger left */}
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

        {/* Logo */}
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

        {/* Desktop nav */}
        <nav
          className={cn("flex-1 items-center justify-center gap-1", showDesktop)}
          onMouseLeave={() => setOpenDrop(null)}
        >
          <NavLink label="Главная" active />
          <NavLink label="Каталог МФО" />

          <div
            className="relative"
            onMouseEnter={() => setOpenDrop("picks")}
          >
            <NavLink label="Подборки" hasDropdown open={openDrop === "picks"} />
            <Dropdown open={openDrop === "picks"} width="w-60">
              {picks.map((p) => (
                <DropdownItem key={p} icon={Sparkles}>
                  {p}
                </DropdownItem>
              ))}
            </Dropdown>
          </div>

          <div
            className="relative"
            onMouseEnter={() => setOpenDrop("amount")}
          >
            <NavLink label="По сумме" hasDropdown open={openDrop === "amount"} />
            <Dropdown open={openDrop === "amount"} width="w-72">
              <div className="grid grid-cols-3 gap-1">
                {amounts.map((a) => (
                  <a
                    key={a}
                    href="#"
                    className="rounded-md px-2 py-2 text-center text-sm font-bold text-brand-ink transition-colors hover:bg-brand-green/10 hover:text-brand-green"
                  >
                    {a}
                  </a>
                ))}
              </div>
            </Dropdown>
          </div>

          <div
            className="relative"
            onMouseEnter={() => setOpenDrop("cities")}
          >
            <NavLink label="По городам" hasDropdown open={openDrop === "cities"} />
            <Dropdown open={openDrop === "cities"} width="w-64">
              {cities.map((c) => (
                <DropdownItem key={c} icon={MapPin}>
                  {c}
                </DropdownItem>
              ))}
              <div className="my-1 h-px bg-brand-line" />
              <a
                href="#"
                className="flex items-center justify-between rounded-md px-3 py-2 text-sm font-bold text-brand-blue transition-colors hover:bg-brand-blue/8"
              >
                Все города
                <span aria-hidden>→</span>
              </a>
            </Dropdown>
          </div>

          <NavLink label="Блог" />
        </nav>

        {/* Desktop search */}
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

        {/* Mobile: search icon right */}
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

      {/* Mobile expandable search */}
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
        <a
          href="#"
          className="flex items-center justify-between rounded-md bg-[#ecfdf5] px-4 py-3.5 text-base font-bold text-[#047857]"
        >
          Главная
        </a>
        <a
          href="#"
          className="mt-1 flex items-center justify-between rounded-md px-4 py-3.5 text-base font-bold text-brand-ink hover:bg-brand-soft"
        >
          Каталог МФО
        </a>

        <MobileSection
          label="Подборки"
          icon={Sparkles}
          open={openSection === "picks"}
          onToggle={() => toggle("picks")}
        >
          {picks.map((p) => (
            <a key={p} href="#" className="block rounded-md px-4 py-2.5 text-sm font-semibold text-brand-muted hover:bg-brand-soft hover:text-brand-blue">
              {p}
            </a>
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
              <a
                key={a}
                href="#"
                className="rounded-md bg-brand-soft px-2 py-2 text-center text-sm font-bold text-brand-ink hover:bg-brand-green/10 hover:text-brand-green"
              >
                {a}
              </a>
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
            <a key={c} href="#" className="block rounded-md px-4 py-2.5 text-sm font-semibold text-brand-muted hover:bg-brand-soft hover:text-brand-blue">
              {c}
            </a>
          ))}
          <a href="#" className="block rounded-md px-4 py-2.5 text-sm font-bold text-brand-blue">
            Все города →
          </a>
        </MobileSection>

        <a href="#" className="mt-1 flex items-center justify-between rounded-md px-4 py-3.5 text-base font-bold text-brand-ink hover:bg-brand-soft">
          Блог
        </a>
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
