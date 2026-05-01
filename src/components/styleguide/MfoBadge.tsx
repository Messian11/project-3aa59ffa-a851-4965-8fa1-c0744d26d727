import { HTMLAttributes } from "react";
import { cn } from "@/lib/utils";

type Tone = "success" | "info" | "warning" | "danger" | "neutral";

interface Props extends HTMLAttributes<HTMLSpanElement> {
  tone?: Tone;
}

const tones: Record<Tone, string> = {
  success: "bg-brand-green/12 text-brand-green ring-1 ring-inset ring-brand-green/20",
  info: "bg-brand-blue/10 text-brand-blue ring-1 ring-inset ring-brand-blue/20",
  warning: "bg-brand-amber/15 text-[#9a6300] ring-1 ring-inset ring-brand-amber/30",
  danger: "bg-brand-danger/10 text-brand-danger ring-1 ring-inset ring-brand-danger/20",
  neutral: "bg-brand-soft text-brand-muted ring-1 ring-inset ring-brand-line",
};

export function MfoBadge({ className, tone = "neutral", ...props }: Props) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-1.5 rounded-pill px-3 py-1 text-xs font-semibold",
        tones[tone],
        className,
      )}
      {...props}
    />
  );
}
