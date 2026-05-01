import { ButtonHTMLAttributes, forwardRef } from "react";
import { cn } from "@/lib/utils";

type Variant = "primary" | "secondary" | "outline" | "ghost";
type Size = "sm" | "md" | "lg";

interface Props extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant;
  size?: Size;
}

const base =
  "inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-blue disabled:opacity-50 disabled:pointer-events-none active:scale-[0.98] rounded-md whitespace-nowrap";

const variants: Record<Variant, string> = {
  primary:
    "bg-brand-green text-brand-green-foreground shadow-card hover:bg-brand-green/90 hover:shadow-hover",
  secondary:
    "bg-brand-blue text-brand-blue-foreground shadow-card hover:bg-brand-blue/90 hover:shadow-hover",
  outline:
    "border border-brand-line bg-white text-brand-ink hover:border-brand-blue hover:text-brand-blue",
  ghost:
    "bg-transparent text-brand-ink hover:bg-brand-soft",
};

const sizes: Record<Size, string> = {
  sm: "h-9 px-4 text-sm",
  md: "h-11 px-5 text-[15px]",
  lg: "h-14 px-7 text-base",
};

export const MfoButton = forwardRef<HTMLButtonElement, Props>(
  ({ className, variant = "primary", size = "md", ...props }, ref) => (
    <button
      ref={ref}
      className={cn(base, variants[variant], sizes[size], className)}
      {...props}
    />
  ),
);
MfoButton.displayName = "MfoButton";
