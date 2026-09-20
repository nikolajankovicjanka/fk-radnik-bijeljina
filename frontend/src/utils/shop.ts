export function getShopImageUrl(path: string | null | undefined): string | null {
  if (!path) {
    return null
  }

  // Već kompletan URL
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }

  // Backend već vratio /storage/...
  if (path.startsWith('/storage/')) {
    return path
  }

  // Ukloni eventualni početni /
  const cleanPath = path.replace(/^\/+/, '')

  return `/storage/${cleanPath}`
}
