export type NewsCategory = "first_team" | "youth" | "women" | "club";

export type NewsItem = {
    id: string;
    title: string;
    excerpt: string;
    image: string;
    date: string;
    slug: string;
    category: NewsCategory;
    tags?: string[];
};
