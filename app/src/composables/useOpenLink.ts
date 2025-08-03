export const useOpenLink = (link: string) => {
  if (!link.trim()) return;

  window.open(link, '_blank');
};
