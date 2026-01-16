export type NewsItem = {
    id: string
    title: string
    excerpt: string
    image: string // /public path ili full url
    date: string  // ISO ili display string
    slug: string
    tags?: string[]
}
