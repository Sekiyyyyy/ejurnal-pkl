"use client";

import { CheckCircle2, BellIcon } from "lucide-react";
import { useState } from "react";
import Alert from "@/components/ui/alert";

export function AlertPreview() {
  const [previewKey, setPreviewKey] = useState(0);

  return (
    <div className="relative flex min-h-[300px] items-center justify-center">
      <button
        className="inline-flex items-center rounded-lg bg-neutral-900 px-6 py-3 text-[13px] font-medium text-white dark:bg-neutral-100 dark:text-neutral-900 transition-transform hover:scale-105"
        onClick={() => setPreviewKey((current) => current + 1)}
        type="button"
      >
        <BellIcon size={16} strokeWidth={1.5} className="mr-2" />
        Show alert
      </button>

      {previewKey > 0 ? (
        <Alert
          icon={<CheckCircle2 aria-hidden className="size-[18px]" />}
          key={previewKey}
          message="Your latest updates are now live for the team."
          variant="toast"
          position="top-right"
          title="Changes saved"
        />
      ) : null}
    </div>
  );
}

export default AlertPreview;
