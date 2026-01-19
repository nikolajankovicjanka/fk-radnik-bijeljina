export type NewsCategory = "club" | "first_team" | "Omladinske selekcije" | "women" | "all";

export type NewsItem = {
    id: string;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    image: string;
    publishedAt: string;
    date: string;
    category: NewsCategory;
    categoryLabel: string;
    tags?: string[];
};