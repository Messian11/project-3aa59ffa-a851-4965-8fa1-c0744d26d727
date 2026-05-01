import { HTMLAttributes } from "react";
import { cn } from "@/lib/utils";

type Variant = "default" | "elevated" | "outline";

interface Props extends HTMLAttributes<HTMLDivElement> {
  variant?: Variant;
}

const variants: Record<Variant, string> = {
  default: "bg-white border border-brand-line shadow-card",
  elevated: "bg-white shadow-hover border border-transparent",
  outline: "bg-white border-2 border-brand-line",
};

export function MfoCard({ className, variant = "default", ...props }: Props) {
  return (
    <div
      className={cn(
        "rounded-lg p-6 transition-shadow",
        variants[variant],
        className,
      )}
      {...props}
    />
  );
}
