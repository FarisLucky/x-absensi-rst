import { toast } from "sonner";

export function useToast() {
  const showToast = {
    success: (message, options) => toast.success(message, options),
    error: (message, options) => toast.error(message, options),
    info: (message, options) => toast(message, options),
    warning: (message, options) => toast.warning(message, options),
    promise: (promise, options) => toast.promise(promise, options),
    custom: (jsx, options) => toast.custom(jsx, options),
    dismiss: (id) => toast.dismiss(id),
  };

  return { showToast };
}
