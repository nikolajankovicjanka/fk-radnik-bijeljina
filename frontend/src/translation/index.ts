import sr from './sr'
import srCy from './sr-cy'
import en from './en'
import de from './de'
import fr from './fr'
import es from './es'

export const messages = {
    'sr-Latn': sr,
    'sr-Cyrl': srCy,
    en,
    de,
    fr,
    es,
}

export type SupportedLocale = keyof typeof messages
